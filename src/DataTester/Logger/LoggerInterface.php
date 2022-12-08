<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Logger;

interface LoggerInterface
{
    public function log($logLevel, $logMessage);
}