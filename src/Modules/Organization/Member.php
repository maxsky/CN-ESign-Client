<?php

namespace MaxSky\ESign\Modules\Organization;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;
use MaxSky\ESign\Validation\OrganizationMemberValidation;

/**
 * 企业机构成员服务API
 *
 * @author   天音
 * @date     2022/09/02 9:51
 *
 * @modifier Max Sky
 * @date     2024/04/26 8:43
 */
class Member extends BaseModule {

    use OrganizationMemberValidation;

    const ESIGN_API_ORG_ADMIN = '/v3/organizations/%s/administrators';
    const ESIGN_API_ORG_MEMBER_LIST = '/v3/organizations/%s/member-list';
    const ESIGN_API_ORG_MEMBER_PERSON = '/v3/organizations/member';
    const ESIGN_API_ORG_MEMBER = '/v3/organizations/%s/members';

    /**
     * 查询企业管理员
     * /v3/organizations/{orgId}/administrators
     *
     * @url https://open.esign.cn/doc/opendoc/employee/fxm4ii
     *
     * @param string $org_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryAdmin(string $org_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_ORG_ADMIN, $org_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET');
    }

    /**
     * 查询企业成员列表
     * /v3/organizations/{orgId}/member-list
     *
     * @url https://open.esign.cn/doc/opendoc/employee/bzrzic
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryMemberList(string $org_id,
                                    int    $page_num = 1, int $page_size = 100): ESignResponse {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER_LIST, $org_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET', [
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ]);
    }

    /**
     * 查询个人用户是否为企业成员
     * /v3/organizations/member
     *
     * @url https://open.esign.cn/doc/opendoc/employee/smmg6dwz7fyys2wl
     *
     * @param string $org_id
     * @param string $psn_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryPerson(string $org_id, string $psn_id): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_MEMBER_PERSON, 'GET', [
            'orgId' => $org_id,
            'psnId' => $psn_id
        ]);
    }

    /**
     * 添加企业机构成员
     * /v3/organizations/{orgId}/members
     *
     * @url https://open.esign.cn/doc/opendoc/employee/has759
     *
     * @param string $org_id
     * @param array  $members
     *
     * @return ESignResponse
     * @throws ESignRequestParameterException
     * @throws ESignResponseException
     */
    public function addMember(string $org_id, array $members): ESignResponse {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER, $org_id);

        $this->validateAddMember($members);

        $params = ['members' => $members];

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params);
    }

    /**
     * 移除企业机构成员
     * /v3/organizations/{orgId}/members
     *
     * https://open.esign.cn/doc/opendoc/employee/tz2uqp
     *
     * @param string $org_id
     * @param array  $member_psn_ids
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function deleteMember(string $org_id, array $member_psn_ids): ESignResponse {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER, $org_id);

        $params = [
            'memberPsnIds' => implode(',', $member_psn_ids)
        ];

        return ESignHttpHelper::doCommHttp($uri, 'DELETE', $params);
    }
}
