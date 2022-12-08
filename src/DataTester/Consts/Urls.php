<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Consts;

/**
 * meta hosts and track event urls
 *
 * default value for CN SaaS
 * SG Saas BASE_URL_I18N => BASE_URL，EVENT_URL_I18N => EVENT_URL
 *
 * onpremise product host => BASE_URL， sdk host/v2/event/list => EVENT_URL
 * Example：product host = product.cc，sdk host = product.com
 * const BASE_URL = 'https://product.cc';
 * const EVENT_URL = 'https://product.com/v2/event/list';
 */
class Urls
{
    const BASE_URL = 'https://data.bytedance.com';

    const BASE_URL_I18N = 'https://datarangers.com';

    // meta uri
    const META_ENDPOINT = '/abmeta/v2/get_abtest_info/';

    const EVENT_URL = 'https://mcs.ctobsnssdk.com/v2/event/list';

    const EVENT_URL_I18N = 'https://mcs.tobsnssdk.com/v2/event/list';
}