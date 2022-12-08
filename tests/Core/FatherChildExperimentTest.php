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

class FatherChildExperimentTest extends TestCase
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
            $configArr = $this->_abClient->getExperimentConfigs("77774", $decisionId, $attributes);
            if ($configArr == null) {
                continue;
            }
            if ($configArr["child_01"]["vid"] == "120311") {
                $fatherConfigArr = $this->_abClient->getExperimentConfigs("77773", $decisionId, $attributes);
                $this->assertTrue($fatherConfigArr["father"]["vid"] == "120309");
                $i++;
            } else if ($configArr["child_01"]["vid"] == "120312") {
                $fatherConfigArr = $this->_abClient->getExperimentConfigs("77773", $decisionId, $attributes);
                $this->assertTrue($fatherConfigArr["father"]["vid"] == "120310");
                $j++;
            }
        }
    }
}