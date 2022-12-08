<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Event;

use DataTester\Consts\Config;
use DataTester\Consts\EventName;
use DataTester\Consts\IdType;

/**
 * abtest_exposure
 */
class EventBuilder
{
    /**
     * @var false
     */
    private $_supportAnonymousEvent;

    /**
     * 1:SaaS 0:onpremise
     * @var bool
     */
    private $_isSaas;

    public function __construct()
    {
        $this->_supportAnonymousEvent = false;
        $this->_isSaas = true;
    }

    /**
     * @param $supportAnonymousEvent
     * @param $isSaas
     * @return void
     */
    public function setConfig($supportAnonymousEvent, $isSaas)
    {
        $this->_supportAnonymousEvent = $supportAnonymousEvent;
        $this->_isSaas = $isSaas;
    }

    public function createExposureEvent($abVersions, $trackId, $attributes): array
    {
        if ($this->_supportAnonymousEvent && $trackId == "") {
            return $this->createAnonymousExposureEvent($abVersions, $trackId, $attributes);
        }
        $localTime = time() * 1000;
        return [
            "events" => [
                [
                    "event" => EventName::EXPOSURE_EVENT,
                    "params" => "{\"datatester_sdk_version\":\"".Config::VERSION."\",\"datatester_sdk_language\":\"php\"}",
                    "local_time_ms" => $localTime,
                    "ab_sdk_version" => (string)$abVersions
                ]
            ],
            "user" => [
                "user_unique_id" => (string)$trackId
            ],
            "header" => [
                "timezone" => 8
            ]
        ];
    }

    public function createAnonymousExposureEvent($abVersions, $trackId, $attributes): array
    {
        $localTime = time() * 1000;
        $event =  [
            "events" => [
                [
                    "event" => EventName::EXPOSURE_EVENT,
                    "params" => "{\"datatester_sdk_version\":\"".Config::VERSION."\",\"datatester_sdk_language\":\"php\"}",
                    "local_time_ms" => $localTime,
                    "ab_sdk_version" => (string)$abVersions
                ]
            ],
            "user" => [
                "user_unique_id" => (string)$trackId
            ],
            "header" => [
                "timezone" => 8
            ]
        ];
        $deviceId = $this->getIdByType(IdType::DEVICE_ID, $attributes);
        // SaaS device_id、bddid、web_id can be set
        // onpremise set device_id according to priority(device_id>bddid>web_id)
        if ($this->_isSaas) {
            if ($deviceId != -1) {
                $event["user"][IdType::DEVICE_ID] = $deviceId;
            }
            $bdDid = $this->getBdDid($attributes);
            if ($bdDid != "") {
                $event["user"][IdType::BDDID] = $bdDid;
            }
            $webId = $this->getIdByType(IdType::WEB_ID, $attributes);
            if ($webId != -1) {
                $event["user"][IdType::WEB_ID] = $webId;
            }
        } else {
            if ($deviceId != -1) {
                $event["user"][IdType::DEVICE_ID] = $deviceId;
            } else {
                $bdDid = $this->getBdDid($attributes);
                if ($bdDid != "" && (int)$bdDid != 0 && (int)$bdDid != -1) {
                    $event["user"][IdType::DEVICE_ID] = (int)$bdDid;
                }
            }
            $webId = $this->getIdByType(IdType::WEB_ID, $attributes);
            if ($webId != -1) {
                $event["user"][IdType::WEB_ID] = $webId;
            }
        }
        return $event;
    }

    private function getIdByType($type, $attributes): int
    {
        $value = $attributes[$type] ?? null;
        if ($value == null) {
            return -1;
        }
        if (is_int($value)) {
            return $value;
        }
        return -1;
    }

    private function getBdDid($attributes): string
    {
        $value = $attributes[IdType::BDDID] ?? null;
        if ($value == null) {
            return "";
        }
        if (is_string($value)) {
            return $value;
        }
        return "";
    }
}