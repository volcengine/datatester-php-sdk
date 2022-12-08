<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Meta;

interface ProductConfigManagerInterface
{
    /**
     * @return ProductConfig|null
     */
    public function getConfig(): ?ProductConfig;
}