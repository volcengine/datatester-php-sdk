<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\UserAbInfo;

interface UserAbInfoHandler
{
    /**
     * @param string $decisionId
     * @return string|null
     */
    public function query(string $decisionId): ?string;

    /**
     * @param string $decisionId
     * @param string $experiment2variantStr
     * @return bool
     */
    public function createOrUpdate(string $decisionId, string $experiment2variantStr): bool;

    /**
     * return true if customize this interface
     * @return bool
     */
    public function needPersistData(): bool;
}