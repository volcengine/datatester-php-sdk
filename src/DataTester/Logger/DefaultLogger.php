<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Logger;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * the interface is implemented by default, you can refer to this interface when you implement it yourself
 */
class DefaultLogger implements LoggerInterface
{
    /**
     * @var Logger Logger instance.
     */
    private $logger;

    /**
     * DefaultLogger constructor.
     *
     * @param int $minLevel Minimum level of messages to be logged.
     * @param string $stream The PHP stream to log output.
     * @throws Exception
     */
    public function __construct(int $minLevel = Logger::INFO, $stream = "stdout")
    {
        $streamHandler = new StreamHandler("php://{$stream}", $minLevel);
        $this->logger = new Logger('Datatester');
        $this->logger->pushHandler($streamHandler);
    }

    public function log($logLevel, $logMessage)
    {
        $this->logger->log($logLevel, $logMessage);
    }
}