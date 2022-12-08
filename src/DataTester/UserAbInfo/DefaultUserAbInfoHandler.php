<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\UserAbInfo;

/**
 * the interface is implemented by default, you can refer to this interface when you implement it yourself
 */
class DefaultUserAbInfoHandler implements UserAbInfoHandler
{
    public function query(string $decisionId): ?string
    {
        return "";
    }

    public function createOrUpdate(string $decisionId, string $experiment2variantStr): bool
    {
        return true;
    }

    public function needPersistData(): bool
    {
        return false;
    }
}