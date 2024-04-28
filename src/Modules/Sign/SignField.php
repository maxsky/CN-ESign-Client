<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/29
 * Time: 05:48
 */

namespace MaxSky\ESign\Modules\Sign;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 合同签署服务 API - 签署区
 */
class SignField extends BaseModule {

    const ESIGN_API_SIGN_FIELD = '/v3/sign-flow/%s/signers/sign-fields';

    /**
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/ohzup7
     *
     * @param string $flow_id
     * @param array  $signers
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function appendSignField(string $flow_id, array $signers, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_SIGN_FIELD, $flow_id);

        $params = array_merge([
            'signers' => $signers
        ], $options);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params);
    }

    /**
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/bd27ph
     *
     * @param string $flow_id
     * @param array  $sign_field_ids
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function deleteSignField(string $flow_id, array $sign_field_ids): ESignResponse {
        $uri = sprintf(self::ESIGN_API_SIGN_FIELD, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'DELETE', [
            'signFieldIds' => $sign_field_ids
        ]);
    }
}
