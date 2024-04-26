<?php

namespace MaxSky\ESign\Modules\Organization;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
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
    const ESIGN_API_ORG_MEMBER = '/v3/organizations/%s/members';

    /**
     * 查询企业管理员
     * /v3/organizations/{orgId}/administrators
     *
     * @url https://open.esign.cn/doc/opendoc/employee/fxm4ii
     *
     * @param string $org_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function queryAdmin(string $org_id): array {
        $uri = sprintf(self::ESIGN_API_ORG_ADMIN, $org_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET')->getJson();
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
     * @return array
     * @throws GuzzleException
     */
    public function queryMemberList(string $org_id, int $page_num = 1, int $page_size = 100): array {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER_LIST, $org_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET', [
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ])->getJson();
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
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function add(string $org_id, array $members): array {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER, $org_id);

        $this->validateAddMember($members);

        $params = ['members' => $members];

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params)->getJson();
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
     * @return array
     * @throws GuzzleException
     */
    public function delete(string $org_id, array $member_psn_ids): array {
        $uri = sprintf(self::ESIGN_API_ORG_MEMBER, $org_id);

        $params = [
            'memberPsnIds' => implode(',', $member_psn_ids)
        ];

        return ESignHttpHelper::doCommHttp($uri, 'DELETE', $params)->getJson();
    }
}
