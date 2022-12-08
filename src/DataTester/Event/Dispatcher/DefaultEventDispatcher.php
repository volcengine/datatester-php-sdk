<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Event\Dispatcher;

use DataTester\Consts\HttpConstants;
use DataTester\Consts\Urls;
use DataTester\Error\ErrorConsts;
use DataTester\Logger\DefaultLogger;
use DataTester\Logger\LoggerInterface;
use Exception;
use GuzzleHttp\Client;
use Monolog\Logger;

/**
 * the interface is implemented by default, you can refer to this interface when you implement it yourself
 */
class DefaultEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var LoggerInterface $_logger
     */
    private $_logger;

    /**
     * @var Client
     */
    private $_httpClient;

    private $_token;

    private $_url;

    public function __construct($token, $url = null, LoggerInterface $logger = null)
    {
        $this->_httpClient = new Client();
        $this->_token = $token;
        $this->_url = $url ?: Urls::EVENT_URL;
        $this->_logger = $logger ?: new DefaultLogger();
    }

    public function dispatchEvent($events): bool
    {
        $options = [
            'headers' => [
                "Content-Type" => HttpConstants::CONTENT_TYPE,
                HttpConstants::USER_AGENT => HttpConstants::DEFAULT_USER_AGENT,
                HttpConstants::APP_KEY_NAME => $this->_token
            ],
            'body' => json_encode($events),
            'timeout' => HttpConstants::TIMEOUT,
            'connect_timeout' => HttpConstants::CONNECT_TIMEOUT
        ];
        // skip ssl verify
        //$options['verify'] = false;
        try {
            $response = $this->_httpClient->post($this->_url, $options);
            if ($response->getStatusCode() !== 200) {
                $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::EVENT_DISPATCH_FAIL, __FUNCTION__));
                return false;
            }
        } catch (Exception $exception) {
            $this->_logger->log(Logger::ERROR, $exception);
            $this->_logger->log(Logger::ERROR, sprintf(ErrorConsts::EVENT_DISPATCH_FAIL, __FUNCTION__));
            return false;
        }
        return true;
    }
}