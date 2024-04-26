<?php

namespace MaxSky\ESign\Modules\Auth;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
use MaxSky\ESign\Modules\BaseModule;
use MaxSky\ESign\Validation\PersonAuthValidation;

/**
 * 认证和授权服务 - 个人API
 *
 * @author    陌上
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/26 7:27
 */
class PersonAuth extends BaseModule {

    use PersonAuthValidation;

    const ESIGN_API_PSN_AUTH_URL = '/v3/psn-auth-url';
    const ESIGN_API_PSN_AUTH_INFO = '/v3/persons/{psnId}/authorized-info';
    const ESIGN_API_PSN_IDENTITY_INFO = '/v3/persons/identity-info';

    /**
     * 获取个人认证 & 授权页面链接
     * /v3/psn-auth-url
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/rx8igf
     *
     * @param array $psn_auth_config
     * @param array $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function authUrl(array $psn_auth_config, array $options = []): array {
        $params = array_merge([
            'psnAuthConfig' => $psn_auth_config
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_AUTH_URL, 'POST', $params)->getJson();
    }

    /**
     * 查询个人授权信息
     * /v3/persons/{psnId}/authorized-info
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/nurtvw
     *
     * @param string $psn_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function queryAuthInfo(string $psn_id): array {
        $uri = sprintf(self::ESIGN_API_PSN_AUTH_INFO, $psn_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET')->getJson();
    }

    /**
     * 查询个人认证信息
     * /v3/persons/identity-info
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/vssvtu
     *
     * @param string|null $psn_id
     * @param string|null $psn_account
     * @param string|null $psn_id_card_num
     * @param string|null $psn_id_card_type
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function queryIdentityInfo(?string $psn_id = null,
                                      ?string $psn_account = null,
                                      ?string $psn_id_card_num = null, ?string $psn_id_card_type = null): array {

        $this->validateQueryPsnIdentityInfo($psn_id, $psn_account, $psn_id_card_num, $psn_id_card_type);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_IDENTITY_INFO, 'GET', [
            'psnId' => $psn_id,
            'psnAccount' => $psn_account,
            'psnIDCardNum' => $psn_id_card_num,
            'psnIDCardType' => $psn_id_card_type
        ])->getJson();
    }
}
