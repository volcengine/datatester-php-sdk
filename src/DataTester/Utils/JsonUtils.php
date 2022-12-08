<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Utils;

class JsonUtils
{
    public static function transferJsonStr2Array($jsonStr): array
    {
        if (empty($jsonStr)) {
            return [];
        }
        $array = json_decode($jsonStr, true);
        return $array ?? [];
    }

    public static function transferArray2JsonStr($array): string
    {
        if (empty($array)) {
            return "{}";
        }
        $result = json_encode($array);
        return $result ?? "{}";
    }
}