<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

use DataTester\Consts\Logic;
use DataTester\Utils\MetaUtils;

class Filter
{
    /**
     * @var string
     */
    private $id;

    /**
     * ||
     * @var string
     */
    private $logicOperator;

    /**
     * @var Condition[]
     */
    private $conditions = [];

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLogicOperator(): ?string
    {
        return $this->logicOperator;
    }

    /**
     * @param string|null $logicOperator
     */
    public function setLogicOperator(?string $logicOperator)
    {
        $this->logicOperator = $logicOperator;
    }

    /**
     * @return Condition[]
     */
    public function getConditions(): ?array
    {
        return $this->conditions;
    }

    /**
     * @param Condition[] $conditions
     */
    public function setConditions(?array $conditions)
    {
        if (isset($conditions)) {
            $this->conditions = MetaUtils::generateEntityArray($conditions, Condition::class);
        }
    }

    public function match($attributes): bool
    {
        $match = true;
        foreach ($this->getConditions() as $condition) {
            $match = $condition->match($attributes);
            $logic = $condition->getLogicOperator();
            if ($logic === Logic::AND_LOGIC && $match === false) {
                return false;
            } elseif ($logic === Logic::OR_LOGIC && $match === true) {
                return true;
            }
        }
        return $match;
    }
}