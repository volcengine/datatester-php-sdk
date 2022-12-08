<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Utils;

use Composer\Semver\Comparator;
use DataTester\Consts\CommonConst;
use DataTester\Consts\FilterValueTypeConst;
use DataTester\Consts\Method;
use DataTester\Consts\OP;
use DataTester\Entities\Condition;

class FilterMatchUtils
{
    public static function match(Condition $condition, $attributes): bool
    {
        $configValue = $condition->getValue();
        $op = $condition->getOp();
        $key = $condition->getKey();
        $type = $condition->getType();
        $method = $condition->getMethod();
        if (!isset($key)) {
            return true;
        }

        if ($key == CommonConst::EXPERIMENT_COHORT && $condition->getPropertyType() == CommonConst::EXPERIMENT_COHORT) {
            return FilterMatchUtils::judgeExperimentCohort($condition, $attributes);
        }

        $realValue = $attributes[$key] ?? null;
        if (!FilterMatchUtils::checkType($realValue, $type, $op)) {
            return false;
        }
        switch ($op)
        {
            case OP::GTE:
                return FilterMatchUtils::gte($type, $method, $realValue, $configValue);
            case OP::GT:
                return FilterMatchUtils::gt($type, $method, $realValue, $configValue);
            case OP::LTE:
                return FilterMatchUtils::lte($type, $method, $realValue, $configValue);
            case OP::LT:
                return FilterMatchUtils::lt($type, $method, $realValue, $configValue);
            case OP::IN:
                return FilterMatchUtils::in($type, $realValue, $configValue);
            case OP::NOT_IN:
                return FilterMatchUtils::notIn($type, $realValue, $configValue);
            case OP::IS_NULL:
                return is_null($realValue);
            case OP::NOT_NULL:
                return !is_null($realValue);
            default:
                return false;
        }
    }

    private static function gte($type, $method, $realValue, $configValue): bool
    {
        if (strcmp($type ,FilterValueTypeConst::NUMBER) === 0) {
            return (float)$realValue >= (float)$configValue;
        }
        if ($method === Method::DICT) {
            $result = strcmp($realValue, $configValue);
            return $result >= 0;
        } elseif ($method === Method::VERSION) {
            return Comparator::greaterThanOrEqualTo($realValue, $configValue);
        }
        return false;
    }

    private static function gt($type, $method, $realValue, $configValue): bool
    {
        if (strcmp($type ,FilterValueTypeConst::NUMBER) === 0) {
            return (float)$realValue > (float)$configValue;
        }
        if ($method === Method::DICT) {
            $result = strcmp($realValue, $configValue);
            return $result > 0;
        } elseif ($method === Method::VERSION) {
            return Comparator::greaterThan($realValue, $configValue);
        }
        return false;
    }

    private static function lte($type, $method, $realValue, $configValue): bool
    {
        if (strcmp($type ,FilterValueTypeConst::NUMBER) === 0) {
            return (float)$realValue <= (float)$configValue;
        }
        if ($method === Method::DICT) {
            $result = strcmp($realValue, $configValue);
            return $result <= 0;
        } elseif($method === Method::VERSION) {
            return Comparator::lessThanOrEqualTo($realValue, $configValue);
        }
        return false;
    }

    private static function lt($type, $method, $realValue, $configValue): bool
    {
        if (strcmp($type ,FilterValueTypeConst::NUMBER) === 0) {
            return (float)$realValue < (float)$configValue;
        }
        if ($method === Method::DICT) {
            $result = strcmp($realValue, $configValue);
            return $result < 0;
        } elseif ($method === Method::VERSION) {
            return Comparator::lessThan($realValue, $configValue);
        }
        return false;
    }

    private static function in($type, $realValue, $configValue): bool
    {
        if (!is_array($configValue) || is_null($realValue)) {
            return false;
        }
        if ($type === FilterValueTypeConst::NUMBER) {
            $value2 = array_map(static function ($s)
            {
                return (float)$s;
            }, $configValue);
            return in_array((float)$realValue, $value2, true);
        }
        return in_array($realValue, $configValue, true);
    }

    private static function notIn($type, $realValue, $configValue): bool
    {
        if (!is_array($configValue) || is_null($realValue)) {
            return false;
        }
        if ($type === FilterValueTypeConst::NUMBER) {
            $value2 = array_map(static function ($s) {
                return (float)$s;
            }, $configValue);
            return !in_array((float)$realValue, $value2, true);
        }
        return !in_array($realValue, $configValue, true);
    }

    private static function checkType($value, $type, $op): bool
    {
        if ($type === FilterValueTypeConst::STRING) {
            // type=string and op=is_null, $value can be null
            if ($op == OP::IS_NULL) {
                return is_string($value) || is_null($value);
            } else {
                return is_string($value);
            }
        } elseif ($type === FilterValueTypeConst::NUMBER) {
            return is_int($value) || is_float($value);
        } elseif ($type === FilterValueTypeConst::BOOL) {
            return is_bool($value);
        }
        return false;
    }

    private static function judgeExperimentCohort(Condition $condition, $attributes): bool
    {
        $configValue = $condition->getValue();
        if ($configValue == null) {
            return true;
        }
        $result = true;
        if ($condition->getOp() == OP::IN) {
            $result = false;
        }
        foreach ($configValue as $experimentId) {
            if ($attributes == null || ($attributes[CommonConst::EXPERIMENT_PREFIX. $experimentId] ?? null) == null) {
                continue;
            }
            if ($condition->getOp() == OP::IN) {
                $result = $attributes[CommonConst::EXPERIMENT_PREFIX. $experimentId] || $result;
            } else {
                $result = !$attributes[CommonConst::EXPERIMENT_PREFIX. $experimentId] && $result;
            }
        }
        return $result;
    }
}