<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Distributor;

use DataTester\Logger\LoggerInterface;
use lastguest\Murmur;
use Monolog\Logger;

class BucketService
{
    /**
     * @var int max traffic num
     */
    private static $MAX_TRAFFIC_VALUE = 1000;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @param $bucketKey
     * @return int
     */
    public function generateBucketValue($bucketKey): int
    {
        $hashInt = Murmur::hash3_int($bucketKey);
        $hashInt = unpack("i", pack("i", $hashInt))[1];
        $val = $hashInt% self::$MAX_TRAFFIC_VALUE;
        if ($val < 0) {
            $val += self::$MAX_TRAFFIC_VALUE;
        }
        return $val;
    }

    /**
     * @param $traffics
     * @param $bucketKey
     * @return mixed|null
     */
    public function bucket($traffics, $bucketKey)
    {
        $index = $this->generateBucketValue($bucketKey);
        foreach ($traffics as $traffic) {
            $begin = $traffic['begin'];
            $end = $traffic['end'];
            $id = $traffic['entity_id'];
            if ($index >= $begin && $index < $end) {
                $this->_logger->log(
                    Logger::DEBUG,
                    sprintf("bucketKey:%s hit entity_id:%s", $bucketKey, $id)
                );
                return $id;
            }
        }
        $this->_logger->log(Logger::DEBUG, sprintf("bucketKey:%s hit no entity_id", $bucketKey));
        return null;
    }
}