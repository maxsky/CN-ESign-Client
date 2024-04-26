<?php

namespace MaxSky\ESign\Modules\Auth;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
use MaxSky\ESign\Modules\BaseModule;
use MaxSky\ESign\Validation\OrganizationAuthValidation;

/**
 * 认证和授权服务 - 企业API
 *
 * @author    陌上
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/25 3:49
 */
class OrganizationAuth extends BaseModule {

    use OrganizationAuthValidation;

    const ESIGN_API_ORG_AUTH_URL = '/v3/org-auth-url';
    const ESIGN_API_ORG_AUTH_INFO = '/v3/organizations/%s/authorized-info';
    const ESIGN_API_ORG_IDENTITY_INFO = '/v3/organizations/identity-info';

    /**
     * 获取机构认证 & 授权页面链接
     * /v3/org-auth-url
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/kcbdu7
     *
     * @param array $org_auth_config
     * @param array $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function authUrl(array $org_auth_config, array $options = []): array {
        $params = array_merge([
            'orgAuthConfig' => $org_auth_config
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_AUTH_URL, 'POST', $params)->getJson();
    }

    /**
     * 查询企业授权详情
     * /v3/organizations/{orgId}/authorized-info
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/ytn2tt
     *
     * @param string $org_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function queryAuthInfo(string $org_id): array {
        $uri = sprintf(self::ESIGN_API_ORG_AUTH_INFO, $org_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET')->getJson();
    }

    /**
     * 查询机构认证信息
     * /v3/organizations/identity-info
     *
     * @url https://open.esign.cn/doc/opendoc/auth3/xxz4tc
     *
     * @param string|null $org_id
     * @param string|null $org_name
     * @param string|null $org_id_card_num
     * @param string|null $org_id_card_type
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function queryIdentityInfo(?string $org_id,
                                      ?string $org_name,
                                      ?string $org_id_card_num, ?string $org_id_card_type): array {
        $this->validateQueryOrgIdentityInfo($org_id, $org_name, $org_id_card_num, $org_id_card_type);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_IDENTITY_INFO, 'GET', [
            'orgId' => $org_id,
            'orgName' => $org_name,
            'orgIDCardNum' => $org_id_card_num,
            'orgIDCardType' => $org_id_card_type
        ])->getJson();
    }
}
