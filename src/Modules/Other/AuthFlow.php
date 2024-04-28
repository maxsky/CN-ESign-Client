<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/29
 * Time: 04:26
 */

namespace MaxSky\ESign\Modules\Other;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

class AuthFlow extends BaseModule {

    const ESIGN_API_AUTH_FLOW_QUERY = '/v3/auth-flow/%s';

    /**
     * 查询认证授权流程详情
     * /v3/auth-flow/{authFlowId}
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/hlrs7s
     *
     * @param string $auth_flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryAuthFlow(string $auth_flow_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_AUTH_FLOW_QUERY, $auth_flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET');
    }
}
