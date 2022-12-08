<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Entities;

use DataTester\Utils\MetaUtils;

class Experiment
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
     * @var string
     */
    private $layerId;

    /**
     * @var int
     */
    private $status;

    /**
     * @var <string, Variant>
     */
    private $variants = [];

    /**
     * @var Release
     */
    private $release = [];

    /**
     * @var <string, object>
     */
    private $whiteList;

    /**
     * 1:enable 0:disable
     * @var int
     */
    private $freezeStatus;

    /**
     * 1:enable 0:disable
     * @var int
     */
    private $versionFreezeStatus;

    /**
     * @var int
     */
    private $experimentMode;

    /**
     * 1:enable 0:disable
     * @var int
     */
    private $filterAllowlist = 0;

    /**
     * association experiment ids
     * @var array
     */
    private $associatedRelations = [];

    /**
     * @return int
     */
    public function getFilterAllowlist(): int
    {
        return $this->filterAllowlist;
    }

    /**
     * @param int $filterAllowlist
     */
    public function setFilterAllowlist(int $filterAllowlist): void
    {
        $this->filterAllowlist = $filterAllowlist;
    }

    /**
     * @var string
     */
    private $fatherExperimentId;

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
    public function getLayerId(): ?string
    {
        return $this->layerId;
    }

    /**
     * @param string|null $layerId
     */
    public function setLayerId(?string $layerId)
    {
        $this->layerId = $layerId;
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
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param $variantId
     * @return Variant|null
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

    /**
     * @return Release
     */
    public function getRelease(): Release
    {
        return $this->release;
    }

    /**
     * @param object $release
     */
    public function setRelease($release)
    {
        if (isset($release)) {
            $this->release = MetaUtils::generateEntity($release, Release::class);
        }
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
     * @return int
     */
    public function getFreezeStatus(): ?int
    {
        return $this->freezeStatus;
    }

    /**
     * @param int|null $freezeStatus
     */
    public function setFreezeStatus(?int $freezeStatus)
    {
        $this->freezeStatus = $freezeStatus;
    }

    /**
     * @return int
     */
    public function getVersionFreezeStatus(): ?int
    {
        return $this->versionFreezeStatus;
    }

    /**
     * @param int|null $versionFreezeStatus
     */
    public function setVersionFreezeStatus(?int $versionFreezeStatus)
    {
        $this->versionFreezeStatus = $versionFreezeStatus;
    }

    /**
     * @return int
     */
    public function getExperimentMode(): ?int
    {
        return $this->experimentMode;
    }

    /**
     * @param int|null $experimentMode
     */
    public function setExperimentMode(?int $experimentMode)
    {
        $this->experimentMode = $experimentMode;
    }

    /**
     * @return string
     */
    public function getFatherExperimentId(): ?string
    {
        return $this->fatherExperimentId;
    }

    /**
     * @param string|null $fatherExperimentId
     */
    public function setFatherExperimentId(?string $fatherExperimentId)
    {
        $this->fatherExperimentId = $fatherExperimentId;
    }

    /**
     * @return array
     */
    public function getAssociatedRelations(): array
    {
        return $this->associatedRelations;
    }

    /**
     * @param array $associatedRelations
     */
    public function setAssociatedRelations(array $associatedRelations): void
    {
        $this->associatedRelations = $associatedRelations;
    }
}