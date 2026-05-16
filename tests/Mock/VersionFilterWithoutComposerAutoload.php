<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

use DataTester\Consts\FilterValueTypeConst;
use DataTester\Consts\Method;
use DataTester\Consts\OP;
use DataTester\Entities\Condition;
use DataTester\Utils\FilterMatchUtils;

spl_autoload_register(static function ($class) {
    $prefix = 'DataTester\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = dirname(__DIR__, 2).'/src/DataTester/'.str_replace('\\', '/', $relativeClass).'.php';
    if (file_exists($file)) {
        require $file;
    }
});

if (class_exists('Composer\\Semver\\Comparator', false)) {
    fwrite(STDERR, "Composer Semver should not be loaded in this script.\n");
    exit(1);
}

$condition = new Condition();
$condition->setKey('app_version');
$condition->setOp(OP::GTE);
$condition->setValue('5.7.0');
$condition->setType(FilterValueTypeConst::STRING);
$condition->setMethod(Method::VERSION);

$matches = FilterMatchUtils::match($condition, ['app_version' => '5.7.1']);
$misses = FilterMatchUtils::match($condition, ['app_version' => '5.6.9']);

if (!$matches || $misses) {
    fwrite(STDERR, "Version filter did not match expected app_version values.\n");
    exit(1);
}
