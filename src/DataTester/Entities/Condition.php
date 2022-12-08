<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

use DataTester\Utils\FilterMatchUtils;

class Condition
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $op;

    /**
     * @var object
     */
    private $value;

    /**
     * string boolean number
     * @var string
     */
    private $type;

    /**
     * only for string type
     * @var string
     */
    private $method;

    /**
     * &&
     * @var string
     */
    private $logicOperator;

    /**
     * only for experiment_cohort
     * @var string
     */
    private $propertyType;

    /**
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string|null $key
     */
    public function setKey(?string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getOp(): ?string
    {
        return $this->op;
    }

    /**
     * @param string|null $op
     */
    public function setOp(?string $op)
    {
        $this->op = $op;
    }

    /**
     * @return object
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string|null $method
     */
    public function setMethod(?string $method)
    {
        $this->method = $method;
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

    public function match($attributes): bool
    {
        return FilterMatchUtils::match($this, $attributes);
    }

    /**
     * @return string
     */
    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    /**
     * @param string $propertyType
     */
    public function setPropertyType(string $propertyType): void
    {
        $this->propertyType = $propertyType;
    }
}