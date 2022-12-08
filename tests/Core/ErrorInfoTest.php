<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Core;

use Client\MockMetaConfigManager;
use Client\MockUserAbInfoHandler;
use DataTester\Client\AbClient;
use DataTester\Event\Dispatcher\EventDispatcherInterface;
use DataTester\Logger\DefaultLogger;
use DataTester\Logger\LoggerInterface;
use DataTester\Meta\ProductConfig;
use DataTester\Meta\ProductConfigManagerInterface;
use PHPUnit\Framework\TestCase;

class ErrorInfoTestTest extends TestCase
{
    /**
     * @var AbClient
     */
    private $_abClient;

    protected function setUp(): void
    {
        $logger = new DefaultLogger();
        $this->_userAbInfoHandler = new MockUserAbInfoHandler();
        $this->_abClient = new AbClient("test_token", $logger, new MockMetaConfigManager($logger),
            new MockEventDispatcherError(), null);
    }

    public function testDispatchExposureEvent()
    {
        $attributes = [];
        $val = $this->_abClient->activate("asso", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a" || $val == "b");
    }

    public function testGetExperimentOrFeatureVariant()
    {
        $attributes = [];
        $name = $this->_abClient->getExperimentVariantName("99999", "decisionId", $attributes);
        $this->assertTrue($name == null);
        $name = $this->_abClient->getExperimentVariantNameWithImpression("99999", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($name == null);
        $configArr = $this->_abClient->getExperimentConfigsWithImpression("99999", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr == null);
        $logger = new DefaultLogger();
        $tmpAbClient = new AbClient("test_token", $logger, new MockMetaConfigManagerError($logger),
            new MockEventDispatcherError(), null);
        $name = $tmpAbClient->getExperimentVariantName("99999", "decisionId", $attributes);
        $this->assertTrue($name == null);
        $configArr = $tmpAbClient->getAllExperimentConfigs("decisionId", $attributes);
        $this->assertTrue($configArr == null);
        $configArr = $tmpAbClient->getFeatureConfigsWithImpression("99999", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr == null);
        $configArr = $tmpAbClient->getFeatureConfigs("99999", "decisionId", $attributes);
        $this->assertTrue($configArr == null);
        $configArr = $tmpAbClient->getAllFeatureConfigs("decisionId", $attributes);
        $this->assertTrue($configArr == null);
        $idArr = $tmpAbClient->getEnabledFeatureIds("decisionId", $attributes);
        $this->assertTrue($idArr == null);
        $configArr = $this->_abClient->activateWithoutImpression("invalid_key", "decisionId", $attributes);
        $this->assertTrue($configArr == []);
    }
}

class MockEventDispatcherError implements EventDispatcherInterface
{
    public function dispatchEvent($events): bool
    {
        return false;
    }
}

class MockMetaConfigManagerError implements ProductConfigManagerInterface
{

    /**
     * @var LoggerInterface Logger instance.
     */
    private $_logger;

    public function __construct(LoggerInterface $logger) {
        $this->_logger = $logger;
    }

    public function getConfig(): ?ProductConfig
    {
        return null;
    }
}