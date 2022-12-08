<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Consts;

class CommonConst
{
    /**
     * meet target audience rules
     */
    const NEED_FILTER_ALLOW_LIST = 1;

    /**
     * freeze experiment
     */
    const EXPERIMENT_FREEZE_STATUS = 1;

    /**
     * traffic changes will not affect exposed users
     */
    const EXPERIMENT_VERSION_FREEZE_STATUS = 1;

    const EXPERIMENT_PREFIX = "experiment_";

    const EXPERIMENT_COHORT = "experiment_cohort";
}