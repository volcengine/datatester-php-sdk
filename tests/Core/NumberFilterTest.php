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

class NumberFilterTest extends TestCase
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
        $attributes["number_param"] = 56789;
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes["number_param"] = 123.45;
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
        $attributes["number_param"] = 99999;
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
        $attributes = ["number_param" => 12345];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["number_param" => 12345.001];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["number_param" => 6789];
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
        $attributes = ["number_param" => 345.56];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["number_param" => 346];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["number_param" => 123.45];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["number_param" => 120];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["number_param" => 180];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["number_param" => 300];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
    }
}