<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

class Variant
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $entityId;

    /**
     * @var object
     */
    private $config;

    /**
     * only for father-child experiment
     * @var array
     */
    private $fatherVariants;

    /**
     * @var string
     */
    private $name;

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
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    /**
     * @param string|null $entityId
     */
    public function setEntityId(?string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param object $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getFatherVariants(): ?array
    {
        return $this->fatherVariants;
    }

    /**
     * @param array|null $fatherVariants
     */
    public function setFatherVariants(?array $fatherVariants)
    {
        $this->fatherVariants = $fatherVariants;
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
    public function toConfig(): ?array
    {
        $variantConfig = $this->config;
        $id = $this->id;
        $config = [];
        foreach ($variantConfig as $key=>$value) {
            $item = [
                $key => [
                    "val" => $value['value'] ?? null,
                    "vid" => $id
                ],
            ];
            $config = $item + $config;
        }
        return $config;
    }
}