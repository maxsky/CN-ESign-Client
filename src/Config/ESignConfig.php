<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/25
 * Time: 03:35
 */

namespace MaxSky\ESign\Config;

use MaxSky\ESign\Common\ESignResponse;

class ESignConfig {

    public $appId;
    public $appSecret;
    public $host;
    public $customHeaders = [];
    public $debug = false;
    public $sandbox = false;

    public $reqTimeout = 20; // seconds
    public $reqConnectTimeout = 10; // seconds
    public $reqUploadTimeout = 90; // seconds
    public $reqUploadConnectTimeout = 60; // seconds
    public $reqEnableHttpProxy = false;
    public $reqHttpProxyIp;
    public $reqHttpProxyPort;
    public $reqHttpProxyUsername;
    public $reqHttpProxyPassword;

    public $customResponseClass = ESignResponse::class;
}
