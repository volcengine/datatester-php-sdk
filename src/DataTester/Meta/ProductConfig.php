<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Meta;

use DataTester\Logger\LoggerInterface;
use DataTester\Entities\Experiment;
use DataTester\Entities\Feature;
use DataTester\Entities\Layer;
use DataTester\Utils\MetaUtils;
use Monolog\Logger;

class ProductConfig
{
    /**
     * @var <String, Experiment>
     */
    private $_experiments;

    /**
     * @var <String, Feature>
     */
    private $_features;

    /**
     * @var <String, Layer>
     */
    private $_layers;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    public function __construct($meta, $logger)
    {
        $experiments = $meta['experiments'] ?: [];
        $this->_experiments = MetaUtils::map2EntityMap($experiments, Experiment::class);
        $layers = $meta['layers'] ?: [];
        $this->_layers = MetaUtils::map2EntityMap($layers, Layer::class);
        $features = $meta['features'] ?: [];
        $this->_features = MetaUtils::map2EntityMap($features, Feature::class);
        $this->_logger = $logger;
    }

    /**
     * @return Experiment[]
     */
    public function getExperiments()
    {
        return $this->_experiments;
    }

    /**
     * @return Feature[]
     */
    public function getFeatures()
    {
        return $this->_features;
    }

    public function getLayerById($layerId)
    {
        if (array_key_exists($layerId, $this->_layers)) {
            return $this->_layers[$layerId];
        }
        $this->_logger->log(Logger::DEBUG, sprintf("layerId:%s, is not exist", $layerId));
        return null;
    }

    public function getExperimentById($experimentId)
    {
        if (array_key_exists($experimentId, $this->_experiments)) {
            return $this->_experiments[$experimentId];
        }
        $this->_logger->log(Logger::DEBUG, sprintf("experimentId:%s, is not exist", $experimentId));
        return null;
    }

    public function getFeatureById($featureId)
    {
        if (array_key_exists($featureId, $this->_features)) {
            return $this->_features[$featureId];
        }
        $this->_logger->log(Logger::DEBUG, sprintf("featureId:%s, is not exist", $featureId));
        return null;
    }
}