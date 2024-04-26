<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/26
 * Time: 02:18
 */

namespace MaxSky\ESign\Modules;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Config\ESignConfig;

abstract class BaseModule {

    protected $appSecret;

    public function __construct(ESignConfig $config) {
        ESignHttpHelper::init($config);

        $this->appSecret = $config->appSecret;
    }
}
