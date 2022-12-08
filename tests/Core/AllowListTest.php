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

class AllowListTest extends TestCase
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

    public function test_allow_without_filter()
    {
        $attributes = [];
        $val = $this->_abClient->activate("allow_without_filter", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $val = $this->_abClient->activate("allow_without_filter", "test_user",
            "trackId", $attributes, null);
        $this->assertTrue($val == true);
        $val = $this->_abClient->activate("allow_without_filter", "test_user_02",
            "trackId", $attributes, null);
        $this->assertTrue($val == false);
    }

    public function test_allow_with_filter()
    {
        $attributes = [];
        $val = $this->_abClient->activate("allow_with_filter", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $val = $this->_abClient->activate("allow_with_filter", "test_user",
            "trackId", $attributes, null);
        $attributes = ["str_param" => null];
        $this->assertTrue($val == null);
        $val = $this->_abClient->activate("allow_with_filter", "test_user_02",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
        $attributes = ["str_param" => ""];
        $val = $this->_abClient->activate("allow_with_filter", "test_user",
            "trackId", $attributes, null);
        $this->assertTrue($val == 0);
        $attributes = ["str_param" => "str1"];
        $val = $this->_abClient->activate("allow_with_filter", "test_user_02",
            "trackId", $attributes, null);
        $this->assertTrue($val == 1);
    }
}