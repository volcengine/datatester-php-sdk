<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Meta;

use DataTester\Consts\HttpConstants;
use DataTester\Consts\Urls;
use DataTester\Error\MetaServerException;
use DataTester\Logger\DefaultLogger;
use DataTester\Logger\LoggerInterface;
use Exception;
use GuzzleHttp\Client;
use Monolog\Logger;

/**
 * the interface is implemented by default, you can refer to this interface when you implement it yourself
 */
class HTTPProductConfigManager implements ProductConfigManagerInterface
{
    /**
     * @var Client Guzzle HTTP client to send requests.
     */
    private $_httpClient;

    /**
     * @var String url
     */
    private $_url;

    /**
     * @var LoggerInterface Logger instance.
     */
    private $_logger;

    /**
     * @var string $_token
     */
    private $_token;

    /**
     * @var integer $_lastModifyTime
     */
    private $_lastModifyTime = 0;

    /**
     * @var ProductConfig $_productConfig
     */
    private $_productConfig;

    public function __construct(
        $token,
        $metaHost = null,
        LoggerInterface $logger = null
    ) {
        $this->_logger = $logger ?: new DefaultLogger();
        $this->_url = rtrim($metaHost, '/') ?: Urls::BASE_URL;
        $this->_httpClient = new Client();
        $this->_token = $token;
        try {
            $this->fetchMeta();
        } catch (MetaServerException | Exception $e) {
            $this->_logger->log(
                Logger::ERROR,
                'datatester Meta fetch error, msg : ' . $e->getMessage()
            );
        }
    }

    /**
     * fetch meta
     * @throws Exception
     * @throws MetaServerException
     */
    public function fetchMeta(): array
    {
        $options = [
            'timeout' => HttpConstants::TIMEOUT,
            'connect_timeout' => HttpConstants::CONNECT_TIMEOUT,
            'query' => [
                'token' => $this->_token
            ]
        ];
        // skip ssl verify
        //$options['verify'] = false;
        try {
            $endPoint = $this->_url . Urls::META_ENDPOINT;
            $response = $this->_httpClient->get($endPoint, $options);
        } catch (Exception $exception) {
            $this->_logger->log(
                Logger::ERROR,
                'datatester Meta fetch error, status code : ' . $exception->getCode()
                . '.please check url and appId, then try ary'
            );
            throw $exception;
        }
        $status = $response->getStatusCode();
        if ($status !== 200) {
            $this->_logger->log(Logger::ERROR, 'datatester Meta error, status code:' . $status);
            throw new MetaServerException('datatester meta server error');
        }
        $_meta = json_decode($response->getBody()->getContents(), true);
        $lastModifyTime = $_meta['modify_time'] ?? 0;
        if ($lastModifyTime > $this->_lastModifyTime) {
            $this->_logger->log(Logger::DEBUG, 'metaJson was modified');
            $this->_productConfig = new ProductConfig($_meta, $this->_logger);
        }
        return $_meta;
    }

    public function getConfig(): ?ProductConfig
    {
        return $this->_productConfig;
    }
}