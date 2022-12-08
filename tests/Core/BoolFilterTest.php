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

class BoolFilterTest extends TestCase
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

    public function test_filter_param()
    {
        $attributes = ["str_param" => "fgh"];
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes["bool_param"] = true;
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
        $attributes["bool_param"] = false;
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
    }

    public function test_filter_param_02()
    {
        $attributes = [];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["bool_param" => true];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["bool_param" => false];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
    }

    public function test_filter_param_03()
    {
        $attributes = [];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["bool_param" => true];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["bool_param" => false];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
    }
}