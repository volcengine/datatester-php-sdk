<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Core;

use Client\MockEventDispatcher;
use Client\MockMetaConfigManager;
use DataTester\Client\AbClient;
use DataTester\Logger\DefaultLogger;
use PHPUnit\Framework\TestCase;

class AssociationExperimentTest extends TestCase
{
    /**
     * @var AbClient
     */
    private $_abClient;

    protected function setUp(): void
    {
        $logger = new DefaultLogger();
        $this->_abClient = new AbClient("test_token", $logger, new MockMetaConfigManager($logger),
            new MockEventDispatcher(), null);
    }

    public function test_father_child_relation()
    {
        $attributes = [];
        $i = 0;
        $j = 0;
        for ($count = 0; $i < 3 || $j < 3; $count++) {
            $decisionId = "decisionId". $count;
            $configArr = $this->_abClient->getExperimentConfigs("77779", $decisionId, $attributes);
            $fatherConfigArr = $this->_abClient->getExperimentConfigs("77775", $decisionId, $attributes);
            if ($configArr == null) {
                $this->assertTrue($fatherConfigArr == null);
                $i++;
            } else {
                $this->assertTrue($fatherConfigArr != null);
                $j++;
            }
        }
        $i = 0;
        $j = 0;
        for ($count = 0; $i < 3 || $j < 3; $count++) {
            $decisionId = "decisionId". $count;
            $configArr = $this->_abClient->getExperimentConfigs("77780", $decisionId, $attributes);
            $fatherConfigArr = $this->_abClient->getExperimentConfigs("77775", $decisionId, $attributes);
            if ($configArr == null) {
                $this->assertTrue($fatherConfigArr != null);
                $i++;
            } else {
                $this->assertTrue($fatherConfigArr == null);
                $j++;
            }
        }
    }
}