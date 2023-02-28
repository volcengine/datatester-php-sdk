DataTester PHP SDK
==================

## Limitation
>This SDK is only supported on **PHP 7.1** and later versions.

## Prerequisite
Obtain your project's App Key:
1. Go to the [BytePlus console](https://console.byteplus.com/) and sign in to your account.
2. Under the **Products** section, click **BytePlus Data Intelligence**.
3. On the **Project List** page, for the project you want to integrate the SDK with, under the **Actions** column, click **Details**.
4. On the **Social Media Details** pop-up window, copy the **App Key**.

## Adding dependency
1. Download this sdk to your project's path: git clone https://github.com/volcengine/datatester-php-sdk.git
```
├── src
├── datatester-php-sdk
├── composer.json
├── composer.lock
└── vendor
```
2. Modify the composer.json file by adding the "repositories" structure.
```
"repositories": [
        {
        "type": "path",
        "url": "./datatester-php-sdk/"
        }
    ]
```
3. Install the package locally by running the code below.
```
composer require -W datatester/datatester-php-sdk
```

## Changing the domain name
**The default domain name must be changed as follows**.Source file is [Urls.php].
```
const BASE_URL = 'https://datarangers.com';
const EVENT_URL = 'https://mcs.tobsnssdk.com/v2/event/list';
```

## Using the SDK
The following is a code example of using the PHP SDK.
```
use DataTester\Client\AbClient;

// Initialize the traffic-allocating object
// Find your app key by clicking "details" on the project list page on the BytePlus console
$abClient = new AbClient("${app_key}");
// The second value is the log interface. You can change it if you want
// The third value is for managing meta information. You can change it if you want
// The fourth value is for event tracking. You can change it if you want
// userAbInfoHandler is to ensure that incoming users do not leave the version. If you want to constantly store the information of incoming users, you can implement UserAbInfoHandler by yourself
// $abClient = new AbClient("ede2cd73482xxxxxx05bd1b24c1", $logger, $configManager, $eventDispatcher，$userAbInfoHandler);

// The user identifier for event tracking. You need to replace it with the actual user ID
$trackId = "uuid";

// The local user identifier. Not used for event tracking. You need to replace it with the actual user ID
$decisionId = "decisionID";

// When a user/device is not in this version, then the value of this parameter is returned. You can set its value as "null"
$defaultValue = true;

// User properties. Only used for allocating the traffic
$attributes = [];

// It is recommended that you use this interface
// The first parameter is the experiment key
$value = $abClient->activate(
    "${experiment_key}",
    $decisionId,
    $trackId,
    $attributes,
    $defaultValue);
if ($value) {
// The control version
} else {
// The variant version
}
```

## API reference

### AbClient
A class for traffic allocation during initialization.
```
__construct(
    $token,
    LoggerInterface $logger = null,
    ProductConfigManagerInterface $productConfigManager = null,
    EventDispatcherInterface $eventDispatcher = null,
    UserAbInfoHandler $userAbInfoHandler = null
)
```
#### Parameter description

| Parameter                     | Description                                                                                                                                                                       | Value                            |
|:------------------------------|:----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|:---------------------------------|
| token                         | Your project's App Key. You can obtain it in the Prerequisites section.                                                                                                           | 2b47*****8d78fd718548153901addde |
| LoggerInterface               | The logger interface which has a default value but you can customize it.                                                                                                          | None                             |
| ProductConfigManagerInterface | The experiment's meta-information management interface, which obtains the experiment's information. You can customize it.                                                         | None                             |
| EventDispatcherInterface      | The interface for event tracking. You can customize it.                                                                                                                           | None                             |
| UserAbInfoHandler             | Ensure that incoming users' information about version ID remains unchanged. If you want to constantly store the information of incoming users, you must implement it by yourself. | None                             |

### activate
Obtain a specific experiment version's configuration after traffic allocation and automatically track the exposed events.
>Make sure you fill in the trackId field if you want to track events.
```
activate($variantKey, $decisionId, $trackId, $attributes, $defaultValue): object
```

#### Parameter description

| Parameter       | Description                                                                                                              |
|:----------------|:-------------------------------------------------------------------------------------------------------------------------|
| variantKey      | The key of the experiment version                                                                                        |
| decisionId      | The local user identifier for traffic allocating                                                                         |
| trackId         | The user identifier for event tracking. You need to replace it with the actual user ID                                   |
| defaultValue	   | When a user/device is not in this version, then the value of this parameter is returned. You can set its value as "None" |
| attributes      | User properties                                                                                                          |

#### Returned value
Parameters of the version that a user enters or the default value when a user/device is not in any version. Can be string, number, boolean, and json type. The following is an example:
```
// When the parameter type is string, the returned value is string "a";
// When the parameter type is number, the returned value is float 123.456;
// When the parameter type is boolean, the returned value is boolean true;
// When the parameter type is json, the returned value array ["key" => "a"].
```

### activateWithoutImpression
Obtain a specific experiment version's configuration after traffic allocation but not track the exposed events.
```
activateWithoutImpression($variantKey, $decisionId, $attributes): array
```

#### Parameter description

#### Returned value
Parameters of the version or empty array. The following is an example:
```
variantKey=string:
        [
        'val' => 'b',
        'vid' => '36872'
        ]
variantKey=number:
        [
        'val' => 789.123,
        'vid' => '36872'
        ]
variantKey=json:
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ]
variantKey=boolean:
        [
        'val' => false,
        'vid' => '36872'
        ]
]
variantKey=not_exist_key:
        []
```

### getExperimentVariantName
Obtain the version's name of an experiment that a user enters:
```
getExperimentVariantName($experimentId, $decisionId, $attributes): ?string
```

#### Parameter description
| Parameter    | Description                                     |
|:-------------|:------------------------------------------------|
| experimentId | The experiment ID to which traffic is allocated |

#### Returned value
Name of the version that a user enters or "None" when a user/device is not in any version.

### getExperimentConfigs
Obtain detailed information about the version that a user enters:
```
getExperimentConfigs($experimentId, $decisionId, $attributes): ?array
```

#### Parameter description

#### Returned value
The detailed information about the version that a user enters or 'None' when a user/device is not in any version. The following is an example:
```
[
  'string' => 
        [
        'val' => 'b',
        'vid' => '36872'
        ],
  'number' => 
        [
        'val' => 789.123,
        'vid' => '36872'
        ],    
  'json' => 
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ],
  'boolean' => 
        [
        'val' => false,
        'vid' => '36872'
        ]
]
```

### getAllExperimentConfigs
Obtain detailed information about all the versions in all experiments.
```
getAllExperimentConfigs($decisionId, $attributes): ?array
```

#### Parameter description

#### Returned value
The detailed information about versions that a user enters or empty array when a user/device is not in any version. The following is an example:
```
[
  'string' => 
        [
        'val' => 'b',
        'vid' => '36872'
        ],
  'number' => 
        [
        'val' => 789.123,
        'vid' => '36872'
        ],    
  'json' => 
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ],
  'boolean' => 
        [
        'val' => false,
        'vid' => '36872'
        ],
  'color' => 
        [
        'val' => 'red',
        'vid' => '36875'
        ]
]
```

### getFeatureConfigs
Obtain detailed information about a feature that a user joins.
```
getFeatureConfigs($featureId, $decisionId, $attributes): ?array
```

#### Parameter description
| Parameter | Description              |
|:----------|:-------------------------|
| featureId | The feature's identifier |

#### Returned value
The detailed information about a feature's variant that a user joins. 'None' when a user/device is not in this feature or the feature is disabled. The following is an example:
```
[
  'feature_key' => 
        [
        'val' => 'prod',
        'vid' => '20006421'
        ]
]
```

### getAllFeatureConfigs
Obtain detailed information about all features that a user joins.
```
getAllFeatureConfigs($decisionId, $attributes): ?array
```

#### Parameter description

#### Returned value
The detailed information about all feature variants that a user joins. The following is an example:
```
[
  'feature_key' => 
        [
        'val' => 'prod',
        'vid' => '20006421'
        ],
  'feature_key_color' => 
        [
        'val' => true,
        'vid' => '20006423'
        ]
]
```

### getExperimentVariantNameWithImpression
Obtain the version's name of the experiment that a user enters.
>Interfaces with **WithImpression** automatically track events. Meanwhile, make sure you fill in the **trackId** field in the **activate** interface if you want to track events.
```
getExperimentVariantNameWithImpression($experimentId, $decisionId, $attributes): ?string
```

#### Parameter description

#### Returned value
Name of the version that a user enters or "None" when a user/device is not in any version.

### getExperimentConfigsWithImpression
Obtain detailed information about the version that a user enters.
>Interfaces with **WithImpression** automatically track events. Meanwhile, make sure you fill in the **trackId** field in the **activate** interface if you want to track events.
```
getExperimentConfigsWithImpression($experimentId, $decisionId, $attributes): ?array
```
#### Parameter description

#### Returned value
The detailed information about the version that a user enters or 'None' when a user/device is not in this version. The following is an example:
```
[
  'string' => 
        [
        'val' => 'b',
        'vid' => '36872'
        ],
  'number' => 
        [
        'val' => 789.123,
        'vid' => '36872'
        ],    
  'json' => 
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ],
  'boolean' => 
        [
        'val' => false,
        'vid' => '36872'
        ]
]
```

### getFeatureConfigsWithImpression
Obtain detailed information about a feature that a user joins.
>Interfaces with **WithImpression** automatically track events. Meanwhile, make sure you fill in the **trackId** field in the **activate** interface if you want to track events.
```
getFeatureConfigsWithImpression($featureId, $decisionId, $attributes): ?array
```

#### Parameter description

#### Returned value
The detailed information about a feature's variant that a user joins. 'None' when a user/device is not in this feature or the feature is disabled. The following is an example:
```
[
  'feature_key' => 
        [
        'val' => 'prod',
        'vid' => '20006421'
        ]
]
```

## Others
In order to better use the sdk, some suggestions are provided.

### LoggerInterface
>The log interface provides a default implementation; if there is a business need, you can customize the implementation class processing and use it when instantiating AbClient.

### ProductConfigManagerInterface
>Meta management interface: request the meta service to pull the experiment and feature information under the application. Default implementation pull it in real time every time AbClient is instantiated; if there is a business need, you can customize the implementation class processing, and use it in when instantiating AbClient.

#### Suggestion
>It is recommended to use redis to cache meta information to avoid pulling every time AbClient is initialized.

The following is an example:
```
$client = new AbClient("token", null, new RedisConfigManager("token"));

class RedisConfigManager implements ProductConfigManagerInterface
{
    /**
     * @var ProductConfig $_productConfig
     */
    private $_productConfig;

    /**
     * @var LoggerInterface Logger instance.
     */
    private $_logger;

    /**
     * @var string $_token
     */
    private $_token;

    public function __construct(
        $token
    )
    {
        $this->_logger = new DefaultLogger();
        $this->_token = $token;
    }

    public function getConfig(): ?ProductConfig
    {
        if ($this->_productConfig != null) {
            return $this->_productConfig;
        }
        $valueFromRedis = $this->getValueFromRedis("tester_meta_info");
        // pull meta when redis cache expired
        if ($valueFromRedis == null) {
            $productConfigManger = new HTTPProductConfigManager($this->_token);
            try {
                $metaInfo = $productConfigManger->getMeta();
                $this->setValue2Redis("tester_meta_info", JsonParse::transferArray2JsonStr($metaInfo), 60);
                $this->_productConfig = new ProductConfig($metaInfo, $this->_logger);
                return $this->_productConfig;
            } catch (\Exception $e) {
                return null;
            }
        }
        $metaInfo = JsonParse::transferJsonStr2Array($valueFromRedis);
        $this->_productConfig = new ProductConfig($metaInfo, $this->_logger);
        return $this->_productConfig;
    }

    private function getValueFromRedis(string $key): ?string
    { 
        // need to implement it yourself
        // return redis.get($key);
        return null;
    }

    private function setValue2Redis(string $key, string $value, int $expire)
    {
        // need to implement it yourself
        // redis.set($key, $value, $expire);
    }
}
```

### EventDispatcherInterface
>Event tracking interface: which track exposure events, provides a default implementation, and track in real time when 'activate' and 'WithImpression' interfaces are called; if there are business needs, you can customize the implementation class processing, and use it in when instantiating AbClient.

#### Suggestion
>It is recommended to use mq(kafka/rocketmq) to track events to avoid tracking events by http every time interfaces are called.
1. Send msg to mq when events happen
2. Use other services to consume kafka and track events

The following is an example:
```
$client = new AbClient("token", null, null, new KafkaEventDispatcher());

class KafkaEventDispatcher implements EventDispatcherInterface
{
    public function dispatchEvent($events)
    {
        // need to implement it yourself
        kafka.send(JsonParse::transferArray2JsonStr($events));
    }
}
```

### UserAbInfoHandler
>Maintain user historical decision information; If you need to use the function of '**freezing experiment**' or '**Traffic changes will not affect exposed users**', you can customize the implementation class processing, and use it in when instantiating AbClient.
#### Suggestion
>It is recommended to use redis to cache decision information.

The following is an example:
```
$client = new AbClient("token", null, null, null, new RedisHandler());

class RedisHandler implements UserAbInfoHandler
{
    public function query(string $decisionId): ?string
    {
        // need to implement it yourself
        return redis.get($decisionId);
    }

    public function createOrUpdate(string $decisionId, string $experiment2variantStr): bool
    {
        // need to implement it yourself
        return redis.set($decisionId, $experiment2variantStr);
    }

    public function needPersistData(): bool
    {
        // return true if customize this interface
        return true;
    }
}
```

### Anonymously tracking
>If there is no user_unique_id as trackId, you can use device_id, web_id, bddid（only onpremise） for anonymous tracking.
1. set event builder config
```
enable anonymously tracking
$this->_abClient->setEventBuilderConfig(true, true);
```
2. append device_id, web_id, bddid to $attributes when trackId is empty string
```
$trackId = "";
$attributes["device_id"] = 1234; int
$attributes["web_id"] = 5678; int
$attributes["bddid"] = "91011"; string
```
3. call activate or 'WithImpression' interfaces


DataTester PHP SDK
==================

## 版本需求
>**php7.1**及更高版本

## 准备工作
获取应用的App Key（即SDK使用的token）:
1. 访问[火山引擎](https://www.volcengine.com/)并登录您的账号
2. 进入集团设置页面，找到应用列表-应用ID列
3. 鼠标悬停在应用ID后的感叹号上获取App Key

## 依赖导入
1. 将SDK下载至项目路径下: git clone https://github.com/volcengine/datatester-php-sdk.git
```
├── src
├── datatester-php-sdk
├── composer.json
├── composer.lock
└── vendor
```
2. 修改项目的composer.json文件，添加repositories结构
```
"repositories": [
        {
        "type": "path",
        "url": "./datatester-php-sdk/"
        }
    ]
```
3. 安装本地包
```
composer require -W datatester/datatester-php-sdk
```

## 域名修改
>SaaS-国内: 默认使用的是SaaS国内环境的域名，无需修改
```
const BASE_URL = 'https://data.bytedance.com';
const EVENT_URL = 'https://mcs.ctobsnssdk.com/v2/event/list';
```
>SaaS-海外: 海外环境需要修改BASE_URL和EVENT_URL，替换为BASE_URL_I18N与EVENT_URL_I18N即可
```
修改如下
const BASE_URL = 'https://datarangers.com';
const EVENT_URL = 'https://mcs.tobsnssdk.com/v2/event/list';
```
>私有化: 私有化部署时会有产品域名和上报域名，如下图所示，BASE_URL替换为产品域名，EVENT_URL替换为上报域名
```
例如：产品域名为product.cc，上报域名为product.com，则修改如下
const BASE_URL = 'https://product.cc';
const EVENT_URL = 'https://product.com/v2/event/list';
```

## 代码示例
```
use DataTester\Client\AbClient;

// 初始化ABTest分流类，token获取方式详见接口描述-AbClient
$abClient = new AbClient("ede2cd734827cccf9c051005bd1b24c1");
// 第2个缺省值，日志接口，可根据业务需要传入自定义实现类，SDK提供默认实现
// 第3个缺省值，实验Meta信息管理接口，可根据业务需要传入自定义实现类，SDK提供默认实现
// 第4个缺省值，进组曝光事件上报接口，可根据业务需要传入自定义实现类，SDK提供默认实现
// 第5个缺省值，进组信息持久化接口，可根据业务需要传入自定义实现类，SDK提供默认实现（不持久化）
// $abClient = new AbClient("ede2cd734827cccf9c051005bd1b24c1", $logger,      $configManager, $eventDispatcher，$userAbInfoHandler);

// trackId 事件上报用户标识，用于事件上报，请替换为客户的真实用户标识
$trackId = "uuid";
// decisionID: 本地分流用户标识，不用于事件上报，请替换为客户的真实用户标识
$decisionId = "decisionID";
// defaultValue: 当分流未命中时返回该值，根据业务需要使用，可传null
$defaultValue = "default_value";
// attributes: 用户属性，仅用于分流，不随埋点上报，可参考https://www.volcengine.com/docs/6287/65826
$attributes = [];

// 推荐接口 abtest_param为需要通过分流下发的参数名称
$value = $abClient->activate(
    "abtest_param",
    $decisionId,
    $trackId,
    $attributes,
    $defaultValue);
if ($value === "param_raw") {
// 对照组
} elseif ($value === "param_test") {
// 实验组
} else {
// 默认处理
}
```

## 接口描述

### AbClient
初始化ABTest分流类
```
__construct(
    $token,
    LoggerInterface $logger = null,
    ProductConfigManagerInterface $productConfigManager = null,
    EventDispatcherInterface $eventDispatcher = null,
    UserAbInfoHandler $userAbInfoHandler = null
)
```
#### 参数

| 参数                            | 描述                                           | 值                                |
|:------------------------------|:---------------------------------------------|:---------------------------------|
| token                         | 获取到的App Key                                  | 2b47*****8d78fd718548153901addde |
| LoggerInterface               | 日志接口，提供默认实现，如有业务需要可自行实现                      | None                             |
| ProductConfigManagerInterface | meta信息获取接口，提供默认实现，如有业务需要可自行实现                | None                             |
| EventDispatcherInterface      | 事件上报接口，提供默认实现，如有业务需要可自行实现                    | None                             |
| UserAbInfoHandler             | 用户进组信息管理接口，提供默认实现，实验冻结和进组不出组场景下需自行实现         | None                             |

### activate
获取特定key的分流结果，并上报曝光事件
```
activate($variantKey, $decisionId, $trackId, $attributes, $defaultValue): object
```

#### 参数

| 参数            | 描述       |
|:--------------|:---------|
| variantKey    | 变体的key   |
| decisionId    | 本地分流用户标识 |
| trackId       | 事件上报用户标识 |
| defaultValue	 | 变体默认值    |
| attributes    | 用户属性     |

#### 返回值
该函数返回命中版本的参数值，未命中时返回默认值
```
参数类型为string，返回值为string "a"
参数类型为number，返回值为float  123.456
参数类型为boolean，返回值为boolean true
参数类型为json，返回值为array ["key" => "a"]
```

### activateWithoutImpression
获取特定key的分流结果，且不上报曝光事件
```
activateWithoutImpression($variantKey, $decisionId, $attributes): array
```

#### 参数

#### 返回值
该函数返回命中版本的参数值，未命中时返回空数组
```
variantKey=string:
        [
        'val' => 'b',
        'vid' => '36872'
        ]
variantKey=number:
        [
        'val' => 789.123,
        'vid' => '36872'
        ]
variantKey=json:
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ]
variantKey=boolean:
        [
        'val' => false,
        'vid' => '36872'
        ]
]
variantKey=not_exist_key:
        []
```

### getExperimentVariantName
获取用户命中的特定实验的变体名称
```
getExperimentVariantName($experimentId, $decisionId, $attributes): ?string
```

#### 参数
| 参数           | 描述        |
|:-------------|:----------|
| experimentId | 指定分流的实验Id |

#### 返回值
该函数返回用户命中的特定实验的变体名称

### getExperimentConfigs
获取用户命中的特定实验的变体详情
```
getExperimentConfigs($experimentId, $decisionId, $attributes): ?array
```

#### 参数

#### 返回值
该函数返回命中变体的array对象，表明用户命中某个实验的变体详情，通常仅能命中一个变体
```
[
  'string' => 
        [
        'val' => 'b',
        'vid' => '36872'
        ],
  'number' => 
        [
        'val' => 789.123,
        'vid' => '36872'
        ],    
  'json' => 
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ],
  'boolean' => 
        [
        'val' => false,
        'vid' => '36872'
        ]
]
```

### getAllExperimentConfigs
获取用户命中的所有实验的变体详情
```
getAllExperimentConfigs($decisionId, $attributes): ?array
```

#### 参数

#### 返回值
该函数返回命中变体的array对象，表明用户命中所有实验的变体详情，通常命中多个变体
```
[
  'string' => 
        [
        'val' => 'b',
        'vid' => '36872'
        ],
  'number' => 
        [
        'val' => 789.123,
        'vid' => '36872'
        ],    
  'json' => 
        [
            'val' => 
                [
                  'key' => 'b'
                ],
            'vid' => '36872'
        ],
  'boolean' => 
        [
        'val' => false,
        'vid' => '36872'
        ],
  'color' => 
        [
        'val' => 'red',
        'vid' => '36875'
        ]
]
```

### getFeatureConfigs
获取用户命中的特定feature的变体详情
```
getFeatureConfigs($featureId, $decisionId, $attributes): ?array
```

#### 参数
| 参数        | 描述         |
|:----------|:-----------|
| featureId | feature Id |

#### 返回值
该函数返回命中变体的array对象，表明用户命中某个feature的变体详情，通常仅能命中一个变体
```
[
  'feature_key' => 
        [
        'val' => 'prod',
        'vid' => '20006421'
        ]
]
```

### getAllFeatureConfigs
获取用户命中的所有feature的变体详情
```
getAllFeatureConfigs($decisionId, $attributes): ?array
```

#### 参数

#### 返回值
该函数返回命中变体的array对象，表明用户命中所有feature的变体详情，通常命中多个变体
```
[
  'feature_key' => 
        [
        'val' => 'prod',
        'vid' => '20006421'
        ],
  'feature_key_color' => 
        [
        'val' => true,
        'vid' => '20006423'
        ]
]
```

>1、含有“WithImpression”字样的接口均会自动上报曝光事件 
> 
>2、请务必填写trackId字段，否则会导致上报失效
> 
### getExperimentVariantNameWithImpression
同接口“getExperimentVariantName”

### getExperimentConfigsWithImpression
同接口“getExperimentConfigs”

### getFeatureConfigsWithImpression
同接口“getFeatureConfigs”

## 其他

### LoggerInterface
日志打印接口，提供默认实现；如有业务需要，可自定义实现类处理，实例化AbClient时传入

### ProductConfigManagerInterface
配置管理接口，请求meta服务拉取应用下的实验信息，提供默认实现，每次实例化AbClient时实时拉取；如有业务需要，可自定义实现类处理，实例化AbClient时传入
>PHP本身不支持内存级别的缓存，可以通过文件(大多数第三方库的选择)或者借助Redis等进行缓存，通过定时任务去拉取meta信息，避免实时拉取

使用Redis缓存示例（仅供参考）
```
$client = new AbClient("token", null, new RedisConfigManager("token"));

class RedisConfigManager implements ProductConfigManagerInterface
{
    /**
     * @var ProductConfig $_productConfig
     */
    private $_productConfig;

    /**
     * @var LoggerInterface Logger instance.
     */
    private $_logger;

    /**
     * @var string $_token
     */
    private $_token;

    public function __construct(
        $token
    )
    {
        $this->_logger = new DefaultLogger();
        $this->_token = $token;
    }

    public function getConfig(): ?ProductConfig
    {
        if ($this->_productConfig != null) {
            return $this->_productConfig;
        }
        $valueFromRedis = $this->getValueFromRedis("tester_meta_info");
        // pull meta when redis cache expired
        if ($valueFromRedis == null) {
            $productConfigManger = new HTTPProductConfigManager($this->_token);
            try {
                $metaInfo = $productConfigManger->getMeta();
                $this->setValue2Redis("tester_meta_info", JsonParse::transferArray2JsonStr($metaInfo), 60);
                $this->_productConfig = new ProductConfig($metaInfo, $this->_logger);
                return $this->_productConfig;
            } catch (\Exception $e) {
                return null;
            }
        }
        $metaInfo = JsonParse::transferJsonStr2Array($valueFromRedis);
        $this->_productConfig = new ProductConfig($metaInfo, $this->_logger);
        return $this->_productConfig;
    }

    private function getValueFromRedis(string $key): ?string
    { 
        // need to implement it yourself
        // return redis.get($key);
        return null;
    }

    private function setValue2Redis(string $key, string $value, int $expire)
    {
        // need to implement it yourself
        // redis.set($key, $value, $expire);
    }
}
```

### EventDispatcherInterface
事件上报接口，上报进组曝光事件，提供默认实现，调用activate与WithImpression接口时实时上报；如有业务需要，可自定义实现类处理，实例化AbClient时传入
>不使用扩展PHP并不支持多线程，可以通过第三方库或者使用mq等进行异步发送，避免实时上报

基于kafka等消息队列，在实例化AbClient对象时传入EventDispatcherInterface的实现类;事件直接写入kafka，通过其他服务去消费kafka并上报（上报可参考
DefaultEventDispatcher的实现），写入和消费kafka的逻辑需自行实现
```
$client = new AbClient("token", null, null, new KafkaEventDispatcher());

class KafkaEventDispatcher implements EventDispatcherInterface
{
    public function dispatchEvent($events)
    {
        // need to implement it yourself
        kafka.send(JsonParse::transferArray2JsonStr($events));
    }
}
```

### UserAbInfoHandler
用户信息处理接口，冻结实验、进组不出组场景下使用
>冻结实验和进组不出组需要持久化用户的进组信息，SDK提供的默认实现不进行数据持久化；
如有业务需要，则实现UserAbInfoHandler接口，结合Redis或其他外部存储对用户进组信息进行持久化处理，初始化AbClient时传入。
使用方式：
>1. 初始化AbClient时不传入UserAbInfoHandler，则默认使用空实现，不启用“进组不出组”功能
>2. 继承UserAbInfoHandler接口，自行实现持久化存储；初始化AbClient时通过构造函数传入

使用Redis缓存示例（仅供参考）
```
$client = new AbClient("token", null, null, null, new RedisHandler());

class RedisHandler implements UserAbInfoHandler
{
    public function query(string $decisionId): ?string
    {
        // need to implement it yourself
        return redis.get($decisionId);
    }

    public function createOrUpdate(string $decisionId, string $experiment2variantStr): bool
    {
        // need to implement it yourself
        return redis.set($decisionId, $experiment2variantStr);
    }

    public function needPersistData(): bool
    {
        // return true if customize this interface
        return true;
    }
}
```

### 匿名上报
>获取不到uuid的用户，可以通过填充device_id或者web_id进行事件上报（私有化场景下也支持bddid）
1. 实例化AbClient后修改事件上报相关配置，setEventBuilderConfig第一个参数（true/开启，false/关闭）匿名上报，第二个参数（true/saas，false/私有化）
```
enable anonymously tracking
$this->_abClient->setEventBuilderConfig(true, true);
```
2. 添加device_id, web_id, bddid到用户属性$attributes，trackId固定传入空字符串""
```
$trackId = "";
$attributes["device_id"] = 1234; int
$attributes["web_id"] = 5678; int
$attributes["bddid"] = "91011"; string
```
3. 请求activate或其他'WithImpression'接口即可匿名上报



