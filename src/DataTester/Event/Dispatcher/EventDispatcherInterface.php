<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Event\Dispatcher;

interface EventDispatcherInterface
{
    /**
     * @param $events
     * @return mixed
     */
    public function dispatchEvent($events);
}