<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Utils;

class BucketKeyBuilder
{
    public static function generateKey($decisionId, $seed): string
    {
        return $decisionId. ":". $seed;
    }
}