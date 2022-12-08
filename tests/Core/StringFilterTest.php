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

class StringFilterTest extends TestCase
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
        $attributes = [];
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
        $attributes = ["str_param" => "jll"];
        $val = $this->_abClient->activate("filter_param", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
        $attributes = ["str_param" => "aaa"];
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
        $attributes = ["str_param" => "5.6.7"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["str_param" => "6.6.7"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["str_param" => "2.3.4"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["str_param" => "2.2.3"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["str_param" => "2.3.5"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => "5.6.6"];
        $val = $this->_abClient->activate("filter_param_02", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
    }

    public function test_filter_param_03()
    {
        $attributes = [];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => "str1"];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => "str2"];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => ""];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
        $attributes = ["str_param" => "str3"];
        $val = $this->_abClient->activate("filter_param_03", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a");
    }

    public function test_filter_param_04()
    {
        $attributes = [];
        $val = $this->_abClient->activate("filter_param_04", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => ""];
        $val = $this->_abClient->activate("filter_param_04", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
        $attributes = ["str_param" => "str1"];
        $val = $this->_abClient->activate("filter_param_04", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "b");
    }
}