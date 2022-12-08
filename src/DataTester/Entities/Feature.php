<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

use DataTester\Utils\MetaUtils;

class Feature
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
     * server
     * @var string
     */
    private $sideType;

    /**
     * @var Release[]
     */
    private $releases = [];

    /**
     * @var int
     */
    private $status;

    /**
     * @var <string, object>
     */
    private $whiteList;

    /**
     * @var <string, Variant>
     */
    private $variants = [];

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
     * @return string
     */
    public function getSideType(): ?string
    {
        return $this->sideType;
    }

    /**
     * @param string|null $sideType
     */
    public function setSideType(?string $sideType)
    {
        $this->sideType = $sideType;
    }

    /**
     * @return Release[]
     */
    public function getReleases(): ?array
    {
        return $this->releases;
    }

    /**
     * @param array|null $releases
     */
    public function setReleases(?array $releases)
    {
        if (isset($releases)) {
            $this->releases = MetaUtils::generateEntityArray($releases,  Release::class);
        }
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getWhiteList()
    {
        return $this->whiteList;
    }

    /**
     * @param mixed $whiteList
     */
    public function setWhiteList($whiteList)
    {
        $this->whiteList = $whiteList;
    }

    /**
     * @return mixed
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param $variantId
     * @return Variant
     */
    public function getVariantById($variantId): ?Variant
    {
        return $this->variants[$variantId] ?? null;
    }

    /**
     * @param mixed $variants
     */
    public function setVariants($variants)
    {
        if (isset($variants)) {
            $this->variants = MetaUtils::map2EntityMap($variants, Variant::class);
        }
    }
}