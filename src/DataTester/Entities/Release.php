<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

use DataTester\Consts\Logic;
use DataTester\Utils\MetaUtils;

class Release
{
    /**
     * @var Filter[]
     */
    private $filter = [];

    /**
     * @var object
     */
    private $trafficAllocation;

    /**
     * @return Filter[]
     */
    public function getFilter(): ?array
    {
        return $this->filter;
    }

    /**
     * @param Filter[] $filter
     */
    public function setFilter(?array $filter)
    {
        if (isset($filter)) {
            $this->filter = MetaUtils::generateEntityArray($filter, Filter::class);
        }
    }

    /**
     * @return object
     */
    public function getTrafficAllocation()
    {
        return $this->trafficAllocation;
    }

    /**
     * @param object $trafficAllocation
     */
    public function setTrafficAllocation($trafficAllocation)
    {
        $this->trafficAllocation = $trafficAllocation;
    }

    public function match($attributes): bool
    {
        $match = true;
        foreach ($this->getFilter() as $filter) {
            $match = $filter->match($attributes);
            $logic = $filter->getLogicOperator();
            if ($logic === Logic::AND_LOGIC && $match === false) {
                return false;
            } elseif ($logic === Logic::OR_LOGIC && $match === true) {
                return true;
            }
        }
        return $match;
    }
}