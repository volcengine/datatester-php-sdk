<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Core;

use Client\MockEventDispatcher;
use Client\MockMetaConfigManager;
use Client\MockUserAbInfoHandler;
use DataTester\Client\AbClient;
use DataTester\Logger\DefaultLogger;
use DataTester\UserAbInfo\UserAbInfoHandler;
use PHPUnit\Framework\TestCase;

class UserAbInfoTest extends TestCase
{
    /**
     * @var AbClient
     */
    private $_abClient;

    /**
     * @var UserAbInfoHandler
     */
    private $_userAbInfoHandler;

    protected function setUp(): void
    {
        $logger = new DefaultLogger();
        $this->_userAbInfoHandler = new MockUserAbInfoHandler();
        $this->_abClient = new AbClient("test_token", $logger, new MockMetaConfigManager($logger),
            new MockEventDispatcher(), $this->_userAbInfoHandler);
    }

    public function test_freeze_version()
    {
        $this->_userAbInfoHandler->clearCache();
        $attributes = [];
        $configArr = $this->_abClient->getExperimentConfigs("77773", "decisionId", $attributes);
        $this->assertTrue($configArr["father"]["vid"] == "120310");
        $this->_userAbInfoHandler->clearCache();
        $this->_userAbInfoHandler->createOrUpdate("decisionId", "{\"77773\":\"120309\"}");
        $configArr = $this->_abClient->getExperimentConfigs("77773", "decisionId", $attributes);
        $this->assertTrue($configArr["father"]["vid"] == "120309");
        $this->_userAbInfoHandler->clearCache();
    }

    public function test_freeze_experiment()
    {
        $this->_userAbInfoHandler->clearCache();
        $attributes = [];
        $configArr = $this->_abClient->getExperimentConfigs("77772", "decisionId", $attributes);
        $this->assertTrue($configArr == null);
        $this->_userAbInfoHandler->createOrUpdate("decisionId", "{\"77772\":\"120307\"}");
        $configArr = $this->_abClient->getExperimentConfigs("77772", "decisionId", $attributes);
        $this->assertTrue($configArr["freeze"]["vid"] == "120307");
        $this->_userAbInfoHandler->clearCache();
        $this->_userAbInfoHandler->createOrUpdate("decisionId", "{\"77772\":\"120308\"}");
        $configArr = $this->_abClient->getExperimentConfigs("77772", "decisionId", $attributes);
        $this->assertTrue($configArr["freeze"]["vid"] == "120308");
        $this->_userAbInfoHandler->clearCache();
        $this->_userAbInfoHandler->createOrUpdate("decisionId", "{\"77772\":\"120309\"}");
        $configArr = $this->_abClient->getExperimentConfigs("77772", "decisionId", $attributes);
        $this->assertTrue($configArr == null);
        $this->_userAbInfoHandler->clearCache();
    }
}