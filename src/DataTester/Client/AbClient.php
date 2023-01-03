<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
*/

namespace DataTester\Client;

use DataTester\Distributor\DecisionService;
use DataTester\Entities\Variant;
use DataTester\Error\ErrorConsts;
use DataTester\Event\Dispatcher\DefaultEventDispatcher;
use DataTester\Event\Dispatcher\EventDispatcherInterface;
use DataTester\Event\EventBuilder;
use DataTester\Logger\DefaultLogger;
use DataTester\Logger\LoggerInterface;
use DataTester\Meta\HTTPProductConfigManager;
use DataTester\Meta\ProductConfig;
use DataTester\Meta\ProductConfigManagerInterface;
use DataTester\UserAbInfo\DefaultUserAbInfoHandler;
use DataTester\UserAbInfo\UserAbInfoHandler;
use DataTester\Utils\JsonUtils;
use Monolog\Logger;

class AbClient
{
    /**
     * @var string
     */
    private $_token;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var ProductConfigManagerInterface
     */
    private $_productConfigManger;

    /**
     * @var DecisionService
     */
    private $_decisionService;

    /**
     * @var EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var EventBuilder
     */
    private $_eventBuilder;

    /**
     * @var UserAbInfoHandler
     */
    private $_userAbInfoHandler;

    /**
     * AbClient constructor
     *
     * @param string $token
     * @param LoggerInterface|null $logger
     * @param ProductConfigManagerInterface|null $productConfigManager
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param UserAbInfoHandler|null $userAbInfoHandler
     */
    public function __construct(
        string                        $token,
        LoggerInterface               $logger = null,
        ProductConfigManagerInterface $productConfigManager = null,
        EventDispatcherInterface      $eventDispatcher = null,
        UserAbInfoHandler             $userAbInfoHandler = null
    ) {
        $this->_token = $token;
        $this->_logger = $logger ?? new DefaultLogger();
        $this->_productConfigManger = $productConfigManager ?? new HTTPProductConfigManager($token);
        $this->_eventDispatcher = $eventDispatcher ?? new DefaultEventDispatcher($token);
        $this->_userAbInfoHandler = $userAbInfoHandler ?? new DefaultUserAbInfoHandler();
        $this->_decisionService = new DecisionService($this->_logger);
        $this->_eventBuilder = new EventBuilder();
    }

    /**
     * @param $supportAnonymousEvent
     * @param $isSaas
     * @return void
     */
    public function setEventBuilderConfig($supportAnonymousEvent, $isSaas)
    {
        $this->_eventBuilder->setConfig($supportAnonymousEvent, $isSaas);
    }

    /**
     * @param $variant
     * @param $trackId
     * @param $attributes
     * @return void
     */
    private function dispatchExposureEventVariant($variant, $trackId, $attributes): void
    {
        if (isset($variant)) {
            $abVersions = $variant->getId();
            $this->dispatchExposureEvent($abVersions, $trackId, $attributes);
        }
    }

    /**
     * @param $abVersions
     * @param $trackId
     * @param $attributes
     * @return void
     */
    private function dispatchExposureEvent($abVersions, $trackId, $attributes): void
    {
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        $success = $this->_eventDispatcher->dispatchEvent(array($event));
        if ($success) {
            return;
        }
        $this->_logger->log(Logger::ERROR, sprintf("event dispatch fail track_id: %s", $trackId));
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $attributes
     * @param $experiment2variant 
     * @return Variant|null
     */
    private function getExperimentVariant($experimentId, $decisionId, $attributes, &$experiment2variant): ?Variant
    {
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return null;
        }
        $experiment = $config->getExperimentById($experimentId);
        if (isset($experiment)) {
            return $this->_decisionService->getVariation(
                $this->getProductConfig(),
                $experiment,
                $decisionId,
                $attributes,
                $experiment2variant
            );
        }
        return null;
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @return Variant|null
     */
    public function getExperimentVariantWithImpression($experimentId, $decisionId, $trackId, $attributes): ?Variant
    {
        $experiment2variant = $this->initUserAbInfo($decisionId);
        $variant = $this->getExperimentVariant($experimentId, $decisionId, $attributes, $experiment2variant);
        $this->dispatchExposureEventVariant($variant, $trackId, $attributes);
        $this->updateUserAbInfo($decisionId, $experiment2variant);
        return $variant;
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $attributes
     * @return string|null
     */
    public function getExperimentVariantName($experimentId, $decisionId, $attributes): ?string
    {
        $experiment2variant = $this->initUserAbInfo($decisionId);
        $variant = $this->getExperimentVariant($experimentId, $decisionId, $attributes, $experiment2variant);
        $this->updateUserAbInfo($decisionId, $experiment2variant);
        if (isset($variant)) {
            return $variant->getName();
        }
        return null;
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @return string|null
     */
    public function getExperimentVariantNameWithImpression($experimentId, $decisionId, $trackId, $attributes): ?string
    {
        $variant = $this->getExperimentVariantWithImpression($experimentId, $decisionId, $trackId, $attributes);
        if (isset($variant)) {
            return $variant->getName();
        }
        return null;
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $attributes
     * @return array|null
     */
    public function getExperimentConfigs($experimentId, $decisionId, $attributes): ?array
    {
        $experiment2variant = $this->initUserAbInfo($decisionId);
        $variant = $this->getExperimentVariant($experimentId, $decisionId, $attributes, $experiment2variant);
        $this->updateUserAbInfo($decisionId, $experiment2variant);
        if (isset($variant)) {
            return $variant->toConfig();
        }
        return null;
    }

    /**
     * @param $experimentId
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @return array|null
     */
    public function getExperimentConfigsWithImpression($experimentId, $decisionId, $trackId, $attributes): ?array
    {
        $variant = $this->getExperimentVariantWithImpression($experimentId, $decisionId, $trackId, $attributes);
        if (isset($variant)) {
            return $variant->toConfig();
        }
        return null;
    }

    /**
     * @param $decisionId
     * @param $attributes
     * @return array|null
     */
    public function getAllExperimentConfigs($decisionId, $attributes): ?array
    {
        $experiment2variant = $this->initUserAbInfo($decisionId);
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return [];
        }
        $experiments = $config->getExperiments();
        $configs = [];
        foreach ($experiments as $experiment) {
            $variant = $this->getExperimentVariant($experiment->getId(), $decisionId, $attributes, $experiment2variant);
            $config = isset($variant) ? $variant->toConfig() : null;
            if (isset($config)){
                $configs += $config;
            }
        }
        $this->updateUserAbInfo($decisionId, $experiment2variant);
        return $configs;
    }

    private function getAllExperimentConfigs4Activate($variantKey, $decisionId, $attributes): ?array
    {
        $experiment2variant = $this->initUserAbInfo($decisionId);
        $experiment2variantCopy = $experiment2variant;
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return [];
        }
        $experiments = $config->getExperiments();
        $configs = [];
        $vid2ExperimentId = [];
        foreach ($experiments as $experiment) {
            $variant = $this->getExperimentVariant($experiment->getId(), $decisionId, $attributes, $experiment2variantCopy);
            $config = isset($variant) ? $variant->toConfig() : null;
            if (isset($config)){
                $configs += $config;
                $vid2ExperimentId[$variant->getId()] = $experiment->getId();
            }
        }
        if (array_key_exists($variantKey, $configs)) {
            $value = $configs[$variantKey];
            $vid = $value['vid'];
            $experimentId = $vid2ExperimentId[$vid] ?? null;
            if (isset($experimentId)) {
                $experiment2variant[$experimentId] = $vid;
            }
        }
        $this->updateUserAbInfo($decisionId, $experiment2variant);
        return $configs;
    }

    /**
     * @param $featureId
     * @param $decisionId
     * @param $attributes
     * @return Variant|null
     */
    private function getFeatureVariant($featureId, $decisionId, $attributes): ?Variant
    {
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return null;
        }
        $feature = $config->getFeatureById($featureId);
        if (isset($feature)) {
            return $this->_decisionService->getVariationForFeature($feature, $decisionId, $attributes);
        }
        return null;
    }

    /**
     * @param $featureId
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @return Variant|null
     */
    private function getFeatureVariantWithImpression($featureId, $decisionId, $trackId, $attributes): ?Variant
    {
        $variant = $this->getFeatureVariant($featureId, $decisionId, $attributes);
        $this->dispatchExposureEventVariant($variant, $trackId, $attributes);
        return $variant;
    }

    /**
     * @param $featureId
     * @param $decisionId
     * @param $attributes
     * @return bool
     */
    public function verifyFeatureEnabled($featureId, $decisionId, $attributes): bool
    {
        $variant = $this->getFeatureVariant($featureId, $decisionId, $attributes);
        return isset($variant);
    }

    /**
     * @param $featureId
     * @param $decisionId
     * @param $attributes
     * @return array|null
     */
    public function getFeatureConfigs($featureId, $decisionId, $attributes): ?array
    {
        $variant = $this->getFeatureVariant($featureId, $decisionId, $attributes);
        if (isset($variant)) {
            return $variant->toConfig();
        }
        return null;
    }

    /**
     * @param $featureId
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @return array|null
     */
    public function getFeatureConfigsWithImpression($featureId, $decisionId, $trackId, $attributes): ?array
    {
        $variant = $this->getFeatureVariantWithImpression($featureId, $decisionId, $trackId, $attributes);
        if (isset($variant)) {
            return $variant->toConfig();
        }
        return null;
    }

    /**
     * @param $decisionId
     * @param $attributes
     * @return array|null
     */
    public function getAllFeatureConfigs($decisionId, $attributes): ?array
    {
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return [];
        }
        $features = $config->getFeatures();
        $configs = [];
        foreach ($features as $feature) {
            $config = $this->getFeatureConfigs($feature->getId(), $decisionId, $attributes);
            if (isset($config)) {
                $configs += $config;
            }
        }
        return $configs;
    }

    /**
     * @param $decisionId
     * @param $attributes
     * @return array|null
     */
    public function getEnabledFeatureIds($decisionId, $attributes): ?array
    {
        $config = $this->getProductConfig();
        if ($config === null) {
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::INVALID_CONFIG, __FUNCTION__));
            return [];
        }
        $features = $config->getFeatures();
        $featureIds = [];
        foreach ($features as $feature) {
            $variant = $this->_decisionService->getVariationForFeature($feature, $decisionId, $attributes);
            if (isset($variant)) {
                array_push($featureIds, $feature->getId());
            }
        }
        return $featureIds;
    }

    /**
     * @param $variantKey
     * @param $decisionId
     * @param $trackId
     * @param $attributes
     * @param $defaultValue
     * @return mixed
     */
    public function activate($variantKey, $decisionId, $trackId, $attributes, $defaultValue)
    {
        $configs = $this->getAllExperimentConfigs4Activate($variantKey, $decisionId, $attributes);
        $configs += $this->getAllFeatureConfigs($decisionId, $attributes);
        if (array_key_exists($variantKey, $configs)) {
            $value = $configs[$variantKey];
            $vid = $value['vid'];
            $val = $value['val'];
            $this->dispatchExposureEvent($vid, $trackId, $attributes);
            return $val;
        }
        return $defaultValue;
    }

    /**
     * @param $variantKey
     * @param $decisionId
     * @param $attributes
     * @return array
     */
    public function activateWithoutImpression($variantKey, $decisionId, $attributes): array
    {
        $configs = $this->getAllExperimentConfigs4Activate($variantKey, $decisionId, $attributes);
        $configs += $this->getAllFeatureConfigs($decisionId, $attributes);
        if (array_key_exists($variantKey, $configs)) {
            return $configs[$variantKey];
        }
        return [];
    }

    /**
     * @return ProductConfig|null
     */
    public function getProductConfig(): ?ProductConfig
    {
        $_productConfig = $this->_productConfigManger->getConfig();
        return $_productConfig instanceof ProductConfig ? $_productConfig : null;
    }

    /**
     * @param string $decisionId
     * @return array
     */
    private function initUserAbInfo(string $decisionId): array
    {
        $experiment2variantStr = $this->_userAbInfoHandler->query($decisionId);
        if (empty($experiment2variantStr)) {
            return [];
        }
        return JsonUtils::transferJsonStr2Array($experiment2variantStr);
    }

    /**
     * @param string $decisionId
     * @param array $experiment2variant
     * @return void
     */
    private function updateUserAbInfo(string $decisionId, array $experiment2variant): void
    {
        if (!$this->_userAbInfoHandler->needPersistData()) {
            return;
        }
        $config = $this->getProductConfig();
        if ($config === null) {
            return;
        }
        $experiments = $config->getExperiments();
        $userAbInfo = [];
        foreach ($experiments as $experiment) {
            $variantId = $experiment2variant[$experiment->getId()] ?? null;
            if (isset($variantId)) {
                $userAbInfo[$experiment->getId()] = $variantId;
            }
        }
        $this->_userAbInfoHandler->createOrUpdate($decisionId, JsonUtils::transferArray2JsonStr($userAbInfo));
    }
}