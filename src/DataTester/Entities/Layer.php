<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

class Layer
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $trafficAllocation;

    /**
     * @var array
     */
    private $experimentIds;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getTrafficAllocation(): ?array
    {
        return $this->trafficAllocation;
    }

    /**
     * @param array|null $trafficAllocation
     */
    public function setTrafficAllocation(?array $trafficAllocation)
    {
        $this->trafficAllocation = $trafficAllocation;
    }

    /**
     * @return array
     */
    public function getExperimentIds(): ?array
    {
        return $this->experimentIds;
    }

    /**
     * @param array|null $experimentIds
     */
    public function setExperimentIds(?array $experimentIds)
    {
        $this->experimentIds = $experimentIds;
    }
}