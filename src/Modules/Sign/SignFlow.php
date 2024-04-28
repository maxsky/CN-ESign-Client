<?php

namespace MaxSky\ESign\Modules\Sign;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;
use MaxSky\ESign\Validation\SignFlowValidation;

/**
 * 合同签署服务 API
 *
 * @author    婉兮
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/26 2:41
 */
class SignFlow extends BaseModule {

    use SignFlowValidation;

    const ESIGN_API_CREATE_BY_FILE = '/v3/sign-flow/create-by-file';
    const ESIGN_API_SIGN_URL = '/v3/sign-flow/%s/sign-url';
    const ESIGN_API_BATCH_SIGN_URL = '/v3/sign-flow/batch-sign-url';
    const ESIGN_API_FILE_DOWNLOAD_URL = '/v3/sign-flow/%s/file-download-url';

    const ESIGN_API_FLOW_START = '/v3/sign-flow/%s/start';
    const ESIGN_API_FLOW_FINISH = '/v3/sign-flow/%s/finish';
    const ESIGN_API_FLOW_REVOKE = '/v3/sign-flow/%s/revoke';
    const ESIGN_API_FLOW_DELAY = '/v3/sign-flow/%s/delay';
    const ESIGN_API_FLOW_URGE = '/v3/sign-flow/%s/urge';

    const ESIGN_API_FLOW_DETAIL = '/v3/sign-flow/%s/detail';
    const ESIGN_API_FLOW_LIST = '/v3/sign-flow/sign-flow-list'; // query flow only create from API
    const ESIGN_API_FLOW_ORG_LIST = '/v3/organizations/sign-flow-list';

    const ESIGN_API_FLOW_RESCISSION_URL = '/v3/sign-flow/%s/rescission-url';
    const ESIGN_API_FLOW_RESCISSION_INITIATE = '/v3/sign-flow/%s/initiate-rescission';

    /**
     * 基于文件发起签署
     *
     * @param array $params
     *
     * @return ESignResponse
     * @throws ESignRequestParameterException
     * @throws ESignResponseException
     */
    public function createByFile(array $params): ESignResponse {
        $this->validateCreateByFile($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_CREATE_BY_FILE, 'POST', $params);
    }

    /**
     * 获取合同文件签署链接
     * /v3/sign-flow/{signFlowId}/sign-url
     *
     * @param string $flow_id
     * @param array  $operator
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function getSignUrl(string $flow_id,
                               array  $operator, array $options = []): ESignResponse {
        $params = array_merge([
            'operator' => $operator
        ], $options);

        $uri = sprintf(self::ESIGN_API_SIGN_URL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params);
    }

    /**
     * 获取批量签页面链接（多流程）
     * /v3/sign-flow/batch-sign-url
     *
     * @param string $operator_id
     * @param array  $flow_ids
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignRequestParameterException
     * @throws ESignResponseException
     */
    public function getBatchSignUrl(string $operator_id,
                                    array  $flow_ids, array $options = []): ESignResponse {
        $this->validateBatchSignUrl($flow_ids);

        $params = array_merge([
            'operatorId' => $operator_id,
            'signFlowIds' => $flow_ids
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_BATCH_SIGN_URL, 'POST', $params);
    }

    /**
     * 通过页面发起合同解约
     * /v3/sign-flow/{signFlowId}/rescission-url
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/dy90gx
     *
     * @param string $flow_id
     * @param array  $rescission_initiator
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function getFlowRescissionUrl(string $flow_id,
                                         array  $rescission_initiator, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_RESCISSION_URL, $flow_id);

        $params = array_merge([
            'rescissionInitiator' => $rescission_initiator
        ], $options);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params);
    }

    /**
     * 下载已签署文件及附属材料
     * /v3/sign-flow/{signFlowId}/file-download-url
     *
     * @param string $flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function downloadFile(string $flow_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FILE_DOWNLOAD_URL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET');
    }

    /**
     * 查询签署流程详情
     * /v3/sign-flow/{signFlowId}/detail
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/xxk4q6
     *
     * @param string $flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryFlowDetail(string $flow_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_DETAIL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET');
    }

    /**
     * 查询签署流程列表
     * /v3/sign-flow/sign-flow-list
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/kq4b2e
     *
     * @param int   $page_num
     * @param int   $page_size
     * @param array $options
     *
     * @return ESignResponse
     * @throws ESignRequestParameterException
     * @throws ESignResponseException
     */
    public function queryFlowList(int $page_num, int $page_size, array $options = []): ESignResponse {
        $params = array_merge([
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        $this->validateQueryFlowList($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_FLOW_LIST, 'POST', $params);
    }

    /**
     * 查询集成方企业名下发起的签署流程列表
     * /v3/organizations/sign-flow-list
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/uhma1i
     *
     * @param int   $page_num
     * @param int   $page_size
     * @param array $options
     *
     * @return ESignResponse
     * @throws ESignRequestParameterException
     * @throws ESignResponseException
     */
    public function queryOrganizationFlowList(int $page_num, int $page_size, array $options = []): ESignResponse {
        $params = array_merge([
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        $this->validateQueryFlowList($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_FLOW_ORG_LIST, 'POST', $params);
    }

    /**
     * 开启签署流程
     * /v3/sign-flow/{signFlowId}/start
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/pu4xsx
     *
     * @param string $flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowStart(string $flow_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_START, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST');
    }

    /**
     * 完结签署流程
     * /v3/sign-flow/{signFlowId}/finish
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/ynwqsm
     *
     * @param string $flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowFinish(string $flow_id): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_FINISH, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST');
    }

    /**
     * 撤销签署流程
     * /v3/sign-flow/{signFlowId}/revoke
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/klbicu
     *
     * @param string $flow_id
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowRevoke(string $flow_id, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_REVOKE, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options);
    }

    /**
     * 延长签署截止时间
     * /v3/sign-flow/{signFlowId}/delay
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/idv0fv
     *
     * @param string $flow_id
     * @param int    $sign_flow_expire_time
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowDelay(string $flow_id, int $sign_flow_expire_time): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_DELAY, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', [
            'signFlowExpireTime' => $sign_flow_expire_time
        ]);
    }

    /**
     * 催签流程中签署人
     * /v3/sign-flow/{signFlowId}/urge
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/yws940
     *
     * @param string $flow_id
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowUrge(string $flow_id, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_URGE, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options);
    }

    /**
     * 发起合同解约
     * /v3/sign-flow/{signFlowId}/initiate-rescission
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/rcgt2karhmz75k1i
     *
     * @param string $flow_id
     * @param array  $rescind_file_list
     * @param string $rescind_reason
     * @param array  $rescissionInitiator
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function flowRescission(string $flow_id,
                                   array  $rescind_file_list,
                                   string $rescind_reason,
                                   array  $rescissionInitiator, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_FLOW_RESCISSION_INITIATE, $flow_id);

        $params = array_merge([
            'rescindFileList' => $rescind_file_list,
            'rescindReason' => $rescind_reason,
            'rescissionInitiator' => $rescissionInitiator
        ], $options);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params);
    }
}
