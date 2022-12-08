<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace Event;

use DataTester\Event\EventBuilder;
use PHPUnit\Framework\TestCase;

class EventBuilderTest extends TestCase
{
    /**
     * @var EventBuilder
     */
    private $_eventBuilder;

    protected function setUp(): void
    {
        $this->_eventBuilder = new EventBuilder();
    }

    public function testCreateExposureEvent()
    {
        $attributes = [];
        $abVersions = "123";
        $trackId = "trackId";
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue(sizeof($event["events"]) == 1);
        self::assertTrue($event["events"][0]["ab_sdk_version"] == $abVersions);
    }

    public function testSaasCreateAnonymousExposureEvent()
    {
        $this->_eventBuilder->setConfig(true, true);
        $abVersions = "123";
        $trackId = "trackId";
        $attributes = ["device_id" => 123];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue(sizeof($event["events"]) == 1);
        self::assertTrue($event["events"][0]["ab_sdk_version"] == $abVersions);
        $trackId = "";
        $attributes = ["device_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["device_id" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 1234);
        $attributes = ["device_id" => 9223372036854775807];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 9223372036854775807);
        $attributes = ["device_id" => 9223372036854775808];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["web_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["web_id" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["web_id"] == 1234);
        $attributes = ["web_id" => 9223372036854775807];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["web_id"] == 9223372036854775807);
        $attributes = ["web_id" => 9223372036854775808];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => ""];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => "trackId"];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["bddid"] == "trackId");
        $attributes = ["bddid" => "trackId", "web_id" => 123456, "device_id" => 67891011];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 4);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["bddid"] == "trackId");
        self::assertTrue($event["user"]["web_id"] == 123456);
        self::assertTrue($event["user"]["device_id"] == 67891011);
        $this->_eventBuilder->setConfig(false, true);
    }

    public function testNotSaasCreateAnonymousExposureEvent()
    {
        $this->_eventBuilder->setConfig(true, false);
        $abVersions = "123";
        $trackId = "trackId";
        $attributes = ["device_id" => 123];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue(sizeof($event["events"]) == 1);
        self::assertTrue($event["events"][0]["ab_sdk_version"] == $abVersions);
        $trackId = "";
        $attributes = ["device_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["device_id" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 1234);
        $attributes = ["device_id" => 9223372036854775807];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 9223372036854775807);
        $attributes = ["device_id" => 9223372036854775808];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["web_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["web_id" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["web_id"] == 1234);
        $attributes = ["web_id" => 9223372036854775807];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["web_id"] == 9223372036854775807);
        $attributes = ["web_id" => 9223372036854775808];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => 1234];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => ""];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => "trackId"];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 1);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        $attributes = ["bddid" => "9223372036854775807"];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 9223372036854775807);
        $attributes = ["bddid" => "12345", "web_id" => 56789, "device_id" => 101112];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 3);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 101112);
        self::assertTrue($event["user"]["web_id"] == 56789);
        $attributes = ["bddid" => "12345", "web_id" => 56789, "device_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 3);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["device_id"] == 12345);
        self::assertTrue($event["user"]["web_id"] == 56789);
        $attributes = ["bddid" => "", "web_id" => 56789, "device_id" => null];
        $event = $this->_eventBuilder->createExposureEvent($abVersions, $trackId, $attributes);
        self::assertTrue(sizeof($event["user"]) == 2);
        self::assertTrue($event["user"]["user_unique_id"] == $trackId);
        self::assertTrue($event["user"]["web_id"] == 56789);
        $this->_eventBuilder->setConfig(false, true);
    }
}
