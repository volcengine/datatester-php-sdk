<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Distributor;

use DataTester\Consts\CommonConst;
use DataTester\Consts\ExperimentMode;
use DataTester\Consts\ExperimentStatus;
use DataTester\Consts\SideType;
use DataTester\Logger\LoggerInterface;
use DataTester\Meta\ProductConfig;
use DataTester\Meta\UserAbInfoHandler;
use DataTester\Entities\Experiment;
use DataTester\Entities\Feature;
use DataTester\Entities\Variant;
use DataTester\Utils\BucketKeyBuilder;
use Monolog\Logger;

class DecisionService
{
    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var BucketService
     */
    private $_bucketService;

    public function __construct($logger)
    {
        $this->_logger = $logger;
        $this->_bucketService = new BucketService($logger);
    }

    /**
     * @param ProductConfig $projectConfig
     * @param Experiment $experiment
     * @param $decisionId
     * @param $attributes
     * @param array $experiment2variant
     * @return Variant|null
     */
    public function getVariation(
        ProductConfig $projectConfig,
        Experiment $experiment,
        $decisionId,
        $attributes,
        array &$experiment2variant
    ): ?Variant
    {
        $decisionId = (string) $decisionId;
        if (!isset($experiment) || $experiment->getExperimentMode() !== ExperimentMode::CODE) {
            return null;
        }

        // handle association experiments
        if ($experiment->getAssociatedRelations() != null) {
            foreach ($experiment->getAssociatedRelations() as $associateExperimentId) {
                if ($attributes != null && ($attributes[CommonConst::EXPERIMENT_PREFIX. $associateExperimentId] ?? null) != null) {
                    continue;
                }
                if($attributes == null) {
                    $attributes = [];
                }
                $associateExperiment = $projectConfig->getExperimentById($associateExperimentId);
                $variant = $this->getVariation($projectConfig, $associateExperiment, $decisionId, $attributes, $experiment2variant);
                if ($variant != null) {
                    $attributes[CommonConst::EXPERIMENT_PREFIX. $associateExperimentId] = true;
                    continue;
                }
                $attributes[CommonConst::EXPERIMENT_PREFIX. $associateExperimentId] = false;
            }
        }

        // allow list
        $variant = $this->handleAllowList($experiment, $decisionId, $attributes);
        if (isset($variant)) {
            $experiment2variant[$experiment->getId()] = $variant->getId();
            return $variant;
        }

        // freeze experiment and traffic changes will not affect exposed users.
        if ($experiment->getVersionFreezeStatus() == CommonConst::EXPERIMENT_VERSION_FREEZE_STATUS
            || $experiment->getFreezeStatus() == CommonConst::EXPERIMENT_FREEZE_STATUS) {
            $variantId = $experiment2variant[$experiment->getId()] ?? null;
            $variant = isset($variantId) ? $experiment->getVariantById($variantId) : null;
            if (isset($variant)) {
                return $variant;
            }
            if ($experiment->getFreezeStatus() == CommonConst::EXPERIMENT_FREEZE_STATUS) {
                return null;
            }
        }

        // validating experiments only handle allow list
        if ($experiment->getStatus() !== ExperimentStatus::RUNNING) {
            $this->_logger->log(Logger::DEBUG, sprintf(
                "experiment: %s status is %s, %s",
                $experiment->getName(),
                $experiment->getStatus(),
                __FUNCTION__
            ));
            return null;
        }

        // layer hash -> experiment
        $layerId = $experiment->getLayerId();
        $layer = $projectConfig->getLayerById($layerId);
        if (!isset($layer)) {
            return null;
        }
        $bucketLayerKey = BucketKeyBuilder::generateKey($decisionId, $layer->getName());
        $experimentId = $this->_bucketService->bucket($layer->getTrafficAllocation(), $bucketLayerKey);
        if (!isset($experimentId) || $experimentId !== $experiment->getId()) {
            return null;
        }

        // target audience
        $release = $experiment->getRelease();
        if (!$release->match($attributes)) {
            return null;
        }

        // experiment hash -> variant
        $variantId=null;
        $bucketExperimentKey = BucketKeyBuilder::generateKey($decisionId, $experiment->getName());
        $variantId = $this->_bucketService->bucket($release->getTrafficAllocation(), $bucketExperimentKey);
        $variant = isset($variantId) ? $experiment->getVariantById($variantId) : null;

        // handle father experiments
        $fatherExperimentId = $experiment->getFatherExperimentId();
        if (!isset($fatherExperimentId, $variant) || $fatherExperimentId === "") {
            $experiment2variant[$experiment->getId()] = $variant->getId();
            return $variant;
        }
        $fatherVariantIds = $variant->getFatherVariants();
        if (!isset($fatherVariantIds)) {
            $experiment2variant[$experiment->getId()] = $variant->getId();
            return $variant;
        }
        $fatherExperiment = $projectConfig->getExperimentById($fatherExperimentId);
        $fatherVariant = $this->getVariation($projectConfig, $fatherExperiment, $decisionId, $attributes, $experiment2variant);
        if (!isset($fatherVariant)) {
            return null;
        }
        if (in_array($fatherVariant->getId(), $fatherVariantIds, true)) {
            return $variant;
        }
        return null;
    }

    /**
     * @param Experiment $experiment
     * @param $decisionId
     * @param $attributes
     * @return Variant|null
     */
    public function handleAllowList(Experiment $experiment, $decisionId, $attributes): ?Variant
    {
        $whiteList = $experiment->getWhiteList();
        $variantId = null;
        foreach ($whiteList as $whiteUser => $entityId) {
            if ((string)$whiteUser === $decisionId) {
                $variantId = $entityId;
                break;
            }
        }
        $variant = isset($variantId) ? $experiment->getVariantById($variantId) : null;
        if (is_null($variant)) {
            return null;
        }
        $this->_logger->log(
            Logger::DEBUG,
            sprintf("decisionId: %s is whiteUser of experiment %s", $decisionId, $experiment->getName())
        );
        if ($experiment->getFilterAllowlist() != CommonConst::NEED_FILTER_ALLOW_LIST) {
            return $variant;
        }
        $release = $experiment->getRelease();
        if ($release->match($attributes)) {
            return $variant;
        }
        return null;
    }

    /**
     * @param Feature $feature
     * @param $decisionId
     * @param $attributes
     * @return Variant|null
     */
    public function getVariationForFeature(Feature $feature, $decisionId, $attributes): ?Variant
    {
        $decisionId = (string) $decisionId;
        if ($feature->getSideType() !== SideType::FEATURE_SERVER) {
            return null;
        }

        // allow list
        $whiteList = $feature->getWhiteList();
        $variantId = null;
        foreach ($whiteList as $whiteUser => $entityId) {
            if ((string)$whiteUser === $decisionId) {
                $variantId = $entityId;
                break;
            }
        }
        $variant = isset($variantId) ? $feature->getVariantById($variantId) : null;
        if (isset($variant)) {
            $this->_logger->log(Logger::DEBUG, sprintf(
                "decisionId: %s is whiteUser of feature %s",
                $decisionId,
                $feature->getName()
            ));
            return $variant;
        }
        if ($feature->getStatus() !== 1) {
            return null;
        }

        $bucketKey = BucketKeyBuilder::generateKey($decisionId, $feature->getName());
        $releases = $feature->getReleases();
        foreach ($releases as $release) {
            if (!$release->match($attributes)) {
                continue;
            }
            $variantId = $this->_bucketService->bucket($release->getTrafficAllocation(), $bucketKey);
            if (isset($variantId)) {
                $this->_logger->log(Logger::DEBUG, sprintf("decisionId hit variant %s", $variantId));
                break;
            }
        }
        return isset($variantId) ? $feature->getVariantById($variantId) : null;
    }
}