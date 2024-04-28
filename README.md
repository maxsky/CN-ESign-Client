# CN-ESign-Client

E签宝 PHP 客户端，根据官方 **SaaSAPI_V3_Demo** 优化修改而来

    改得跟官方一点都不像了，LOL :)

## 要求

PHP: `^7.2 || ^8.0`

PECL: `curl && json`

## 安装

```bash
composer require maxsky/cn-esign-client
```

## 使用

### 1. 初始化配置

```php
$config = new \MaxSky\ESign\Config\ESignConfig();

$config->appId = 'test';
$config->appSecret = 'test';

$config->debug = true; // default false
```

### 2. 应用配置

```php
\MaxSky\ESign\ESignOpenAPI::setConfig($config);
```

### 3. 调用服务

```php
$start = Carbon::createFromDate(2024, 3, 1)->startOfDay()->getTimestampMs();
$to = Carbon::createFromDate(2024, 3, 31)->endOfDay()->getTimestampMs();

$response = ESignOpenAPI::signFlow()->queryOrganizationFlowList(1, 10, [
    'signFlowStartTimeFrom' => $start,
    'signFlowStartTimeTo' => $to
]);

/** @var StreamInterface */
$response->getBody(); // return original response 

/** @var array */
$response->getJson(); // return array

/** @var array */
$response->getData(); // return 'data' field content of Json array
```
