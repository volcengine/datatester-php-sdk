<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Client;

require(dirname(__FILE__).'/../Mock/MockMeta.php');
use DataTester\Client\AbClient;
use DataTester\Event\Dispatcher\EventDispatcherInterface;
use DataTester\Logger\DefaultLogger;
use DataTester\Logger\LoggerInterface;
use DataTester\Meta\ProductConfig;
use DataTester\Meta\ProductConfigManagerInterface;
use DataTester\UserAbInfo\UserAbInfoHandler;
use PHPUnit\Framework\TestCase;

class AbClientTest extends TestCase
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
        $this->_abClient->setEventBuilderConfig(false, true);
    }

    public function testGetExperimentVariantWithImpression()
    {
        $attributes = [];
        $variant = $this->_abClient->getExperimentVariantWithImpression("77773", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($variant != null);
        $this->assertArrayHasKey("father", $variant->toConfig());
    }

    public function testVerifyFeatureEnabled()
    {
        $attributes = [];
        $enable = $this->_abClient->verifyFeatureEnabled("10202", "decisionId", $attributes);
        $this->assertTrue($enable);
        $enable = $this->_abClient->verifyFeatureEnabled("10203", "decisionId", $attributes);
        $this->assertFalse($enable);
    }

    public function testGetEnabledFeatureIds()
    {
        $attributes = [];
        $arr = $this->_abClient->getEnabledFeatureIds("decisionId", $attributes);
        $this->assertTrue($arr[0] == "10202");
    }

    public function testGetFeatureConfigsWithImpression()
    {
        $attributes = [];
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "decisionId",
            "trackId", $attributes);
        $this->assertArrayHasKey("feature", $configArr);
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "test_user",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101622");
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "test_user_02",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101623");
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101623");
        $attributes = ["age" => 12347];
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101622");
        $attributes = ["age" => 12345];
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101622");
        $attributes = ["age" => 12344];
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10202", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr["feature"]["vid"] == "20101623");

    }

    public function testGetFeatureConfigs()
    {
        $attributes = [];
        $configArr = $this->_abClient->getFeatureConfigsWithImpression("10203", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($configArr == null);
    }

    public function testGetExperimentConfigsWithImpression()
    {
        $attributes = [];
        $configArr = $this->_abClient->getExperimentConfigsWithImpression("77775", "decisionId",
            "trackId", $attributes);
        $this->assertArrayHasKey("asso", $configArr);
    }

    public function testGetExperimentConfigs()
    {
        $attributes = [];
        $configArr = $this->_abClient->getExperimentConfigs("77775", "decisionId", $attributes);
        $this->assertArrayHasKey("asso", $configArr);
    }

    public function testGetAllExperimentConfigs()
    {
        $attributes = [];
        $configArr = $this->_abClient->getAllExperimentConfigs("decisionId", $attributes);
        $this->assertArrayHasKey("asso", $configArr);
        $this->assertArrayHasKey("father", $configArr);
        $this->assertArrayHasKey("child_01", $configArr);
        $this->assertArrayHasKey("filter_param", $configArr);
    }

    public function testGetExperimentVariantNameWithImpression()
    {
        $attributes = [];
        $name = $this->_abClient->getExperimentVariantNameWithImpression("77775", "decisionId",
            "trackId", $attributes);
        $this->assertTrue($name == "对照版本");
    }

    public function testGetExperimentVariantName()
    {
        $attributes = [];
        $name = $this->_abClient->getExperimentVariantName("77773", "decisionId", $attributes);
        $this->assertTrue($name == "实验版本1");
    }

    public function testActivate()
    {
        $attributes = [];
        $val = $this->_abClient->activate("asso", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == "a" || $val == "b");
        $val = $this->_abClient->activate("asso_invalid", "decisionId",
            "trackId", $attributes, null);
        $this->assertTrue($val == null);
    }

    public function testActivateWithoutImpression()
    {
        $attributes = [];
        $configArr = $this->_abClient->activateWithoutImpression("asso", "decisionId", $attributes);
        $this->assertTrue($configArr["vid"] == "120314" || $configArr["vid"] == "120315");
    }

    public function testGetAllFeatureConfigs()
    {
        $attributes = [];
        $configArr = $this->_abClient->getAllFeatureConfigs("decisionId", $attributes);
        $this->assertArrayHasKey("feature", $configArr);
    }

    public function testGetProductConfig()
    {
        $productConfig = $this->_abClient->getProductConfig();
        $this->assertTrue($productConfig != null);
    }
}

class MockMetaConfigManager implements ProductConfigManagerInterface
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
        $meta = json_decode(MOCK_META, true);
        return new ProductConfig($meta, $this->_logger);
    }
}

class MockEventDispatcher implements EventDispatcherInterface
{

    public function dispatchEvent($events): bool
    {
        return true;
    }
}

class MockUserAbInfoHandler implements UserAbInfoHandler
{

    public function createOrUpdate(string $decisionId, string $experiment2variantStr): bool
    {
        $filePath = dirname(__FILE__).'/../Mock/userInfo.txt';
        $text = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = [];
        if (count($text) > 0) {
            $json = $text[0];
            $data = json_decode($json, true);
        }
        $data[$decisionId] = $experiment2variantStr;
        $result = json_encode($data);
        $myFile = fopen(dirname(__FILE__).'/../Mock/userInfo.txt', "w");
        fwrite($myFile, $result);
        return true;
    }

    public function query(string $decisionId): ?string
    {
        $filePath = dirname(__FILE__).'/../Mock/userInfo.txt';
        $text = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (count($text) > 0) {
            $json = $text[0];
            $data = json_decode($json, true);
            if (array_key_exists($decisionId, $data)) {
                return $data[$decisionId];
            }
        }
        return null;
    }

    public function needPersistData(): bool
    {
        return true;
    }

    public function clearCache()
    {
        $myFile = fopen(dirname(__FILE__).'/../Mock/userInfo.txt', "w");
        fwrite($myFile, "");
    }
}