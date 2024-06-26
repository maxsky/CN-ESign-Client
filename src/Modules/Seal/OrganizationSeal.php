<?php

namespace MaxSky\ESign\Modules\Seal;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 印章服务 - 企业API
 *
 * @author    天音
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/26 9:40
 */
class OrganizationSeal extends BaseModule {

    const ESIGN_API_ORG_SEAL_CREATE_BY_TEMPLATE = '/v3/seals/org-seals/create-by-template';
    const ESIGN_API_ORG_SEAL_CREATE_BY_IMAGE = '/v3/seals/org-seals/create-by-image';
    const ESIGN_API_ORG_SEAL_OWN_LIST = '/v3/seals/org-own-seal-list';
    const ESIGN_API_ORG_SEAL_INFO = '/v3/seals/org-seal-info';
    const ESIGN_API_ORG_SEAL_AUTHED_LIST = '/v3/seals/org-authorized-seal-list';
    const ESIGN_API_ORG_SEAL_AUTH_INTERNAL = '/v3/seals/org-seals/internal-auth';
    const ESIGN_API_ORG_SEAL_AUTH_EXTERNAL = '/v3/seals/org-seals/external-auth';
    const ESIGN_API_ORG_SEAL_AUTH_DELETE = '/v3/seals/org-seals/auth-delete';
    const ESIGN_API_ORG_SEAL = '/v3/seals/org-seal';

    /**
     * 创建机构模板印章
     * /v3/seals/org-seals/create-by-template
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/igfmd2
     *
     * @param string $org_id
     * @param string $seal_name
     * @param string $seal_template_style
     * @param string $seal_size
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function createByTemplate(string $org_id,
                                     string $seal_name,
                                     string $seal_template_style,
                                     string $seal_size, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'sealName' => $seal_name,
            'sealTemplateStyle' => $seal_template_style,
            'sealSize' => $seal_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_CREATE_BY_TEMPLATE, 'POST', $params);
    }

    /**
     * 创建机构图片印章
     * /v3/seals/org-seals/create-by-image
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/lggz9w
     *
     * @param string $org_id
     * @param string $seal_image_file_key
     * @param string $seal_name
     * @param string $seal_width
     * @param string $seal_height
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function createByImage(string $org_id,
                                  string $seal_image_file_key,
                                  string $seal_name,
                                  string $seal_width,
                                  string $seal_height, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'sealImageFileKey' => $seal_image_file_key,
            'sealName' => $seal_name,
            'sealWidth' => $seal_width,
            'sealHeight' => $seal_height
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_CREATE_BY_IMAGE, 'POST', $params);
    }

    /**
     * 查询企业内部印章
     * /v3/seals/org-own-seal-list
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/ups6h1
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryOwnSealList(string $org_id,
                                     int    $page_num = 1, int $page_size = 20, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_OWN_LIST, 'GET', $params);
    }

    /**
     * 查询指定印章详情
     * /v3/seals/org-seal-info
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/picwop
     *
     * @param string $org_id
     * @param string $seal_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function querySealInfo(string $org_id, string $seal_id): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_INFO, 'GET', [
            'orgId' => $org_id,
            'sealId' => $seal_id
        ]);
    }

    /**
     * 查询被外部企业授权印章
     * /v3/seals/org-authorized-seal-list
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/czrua1
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryExternalAuthedList(string $org_id,
                                            int    $page_num = 1,
                                            int    $page_size = 20, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTHED_LIST, 'GET', $params);
    }

    /**
     * 查询对内部成员授权详情
     * /v3/seals/org-seals/internal-auth
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/totfte
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function authInternalQuery(string $org_id,
                                      int    $page_num = 1, int $page_size = 20, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTH_INTERNAL, 'GET', $params);
    }

    /**
     * 查询对外部企业授权详情
     * /v3/seals/org-seals/external-auth
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/ngvb5p
     *
     * @param string $org_id
     * @param string $seal_id
     * @param string $authorized_org_id
     * @param int    $page_num
     * @param int    $page_size
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function authExternalQuery(string $org_id,
                                      string $seal_id,
                                      string $authorized_org_id,
                                      int    $page_num = 1, int $page_size = 20): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTH_INTERNAL, 'GET', [
            'orgId' => $org_id,
            'sealId' => $seal_id,
            'authorizedOrgId' => $authorized_org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ]);
    }

    /**
     * 解除印章授权
     *
     * @param string $org_id
     * @param string $delete_type
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function authDelete(string $org_id,
                               string $delete_type, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'deleteType' => $delete_type
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTH_DELETE, 'POST', $params);
    }

    /**
     * 内部成员授权
     * /v3/seals/org-seals/internal-auth
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/fu6ov5
     *
     * @param string $org_id
     * @param string $seal_id
     * @param array  $authorized_psn_ids
     * @param string $seal_role
     * @param string $transactor_psn_id
     * @param array  $seal_auth_scope
     * @param int    $effective_time
     * @param int    $expire_time
     * @param bool   $auto_sign
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function internalAuth(string $org_id,
                                 string $seal_id,
                                 array  $authorized_psn_ids,
                                 string $seal_role,
                                 string $transactor_psn_id,
                                 array  $seal_auth_scope,
                                 int    $effective_time,
                                 int    $expire_time, bool $auto_sign = false, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'sealId' => $seal_id,
            'authorizedPsnIds' => $authorized_psn_ids,
            'sealRole' => $seal_role,
            'transactorPsnId' => $transactor_psn_id,
            'sealAuthScope' => $seal_auth_scope,
            'effectiveTime' => $effective_time,
            'expireTime' => $expire_time,
            'autoSign' => $auto_sign
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTH_INTERNAL, 'POST', $params);
    }

    /**
     * 跨企业授权
     * /v3/seals/org-seals/external-auth
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/qkxyha
     *
     * @param string $org_id
     * @param string $seal_id
     * @param string $transactor_psn_id
     * @param array  $authorized_org_info
     * @param int    $effective_time
     * @param int    $expire_time
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function externalAuth(string $org_id,
                                 string $seal_id,
                                 string $transactor_psn_id,
                                 array  $authorized_org_info,
                                 int    $effective_time, int $expire_time, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'sealId' => $seal_id,
            'transactorPsnId' => $transactor_psn_id,
            'authorizedOrgInfo' => $authorized_org_info,
            'effectiveTime' => $effective_time,
            'expireTime' => $expire_time
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL_AUTH_EXTERNAL, 'POST', $params);
    }

    /**
     * 删除机构印章
     * /v3/seals/org-seal
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/qdfvs6
     *
     * @param string $org_id
     * @param string $seal_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function deleteSeal(string $org_id, string $seal_id): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SEAL, 'DELETE', [
            'orgId' => $org_id,
            'sealId' => $seal_id
        ]);
    }
}
