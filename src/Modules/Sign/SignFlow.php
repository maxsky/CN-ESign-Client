<?php

namespace MaxSky\ESign\Modules\Sign;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Exceptions\ESignRequestParameterException;
use MaxSky\ESign\Modules\BaseModule;
use MaxSky\ESign\Validation\SignFlowValidation;

/**
 * 合同签署服务API
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
    const ESIGN_API_ORG_SIGN_FLOW_LIST = '/v3/organizations/sign-flow-list';

    const ESIGN_API_SIGN_FIELD_ADD = '/v3/sign-flow/%s/signers/sign-fields';
    const ESIGN_API_SIGN_FIELD_DELETE = '/v3/sign-flow/%s/signers/sign-fields';

    const ESIGN_API_CONTRACT_VERIFY = '/v3/files/%s/verify';
    const ESIGN_API_ANTCHAIN_FILE_INFO = '/v3/antchain-file-info';
    const ESIGN_API_ANTCHAIN_VERIFY = '/v3/antchain-file-info/verify';

    const ESIGN_API_RESCISSION_URL = '/v3/sign-flow/%s/rescission-url';

    /**
     * 基于文件发起签署
     *
     * @param array $params
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function createByFile(array $params): array {
        $this->validateCreateByFile($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_CREATE_BY_FILE, 'POST', $params)->getJson();
    }

    /**
     * 获取合同文件签署链接
     * /v3/sign-flow/{signFlowId}/sign-url
     *
     * @param string $flow_id
     * @param array  $operator
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function getSignUrl(string $flow_id, array $operator, array $options = []): array {
        $params = array_merge([
            'operator' => $operator
        ], $options);

        $uri = sprintf(self::ESIGN_API_SIGN_URL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params)->getJson();
    }

    /**
     * 获取批量签页面链接（多流程）
     * /v3/sign-flow/batch-sign-url
     *
     * @param string $operator_id
     * @param array  $flow_ids
     * @param array  $options
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function getBatchSignUrl(string $operator_id, array $flow_ids, array $options = []): array {
        $this->validateBatchSignUrl($flow_ids);

        $params = array_merge([
            'operatorId' => $operator_id,
            'signFlowIds' => $flow_ids
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_BATCH_SIGN_URL, 'POST', $params)->getJson();
    }

    /**
     * 下载已签署文件及附属材料
     * /v3/sign-flow/{signFlowId}/file-download-url
     *
     * @param string $flow_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function downloadFile(string $flow_id): array {
        $uri = sprintf(self::ESIGN_API_FILE_DOWNLOAD_URL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET')->getJson();
    }

    /**
     * @param string $flow_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function flowStart(string $flow_id): array {
        $uri = sprintf(self::ESIGN_API_FLOW_START, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST')->getJson();
    }

    /**
     * 完结签署流程
     * /v3/sign-flow/{signFlowId}/finish
     *
     * @param string $flow_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function flowFinish(string $flow_id): array {
        $uri = sprintf(self::ESIGN_API_FLOW_FINISH, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST')->getJson();
    }

    /**
     * 撤销签署流程
     * /v3/sign-flow/{signFlowId}/revoke
     *
     * @param string $flow_id
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function flowRevoke(string $flow_id, array $options = []): array {
        $uri = sprintf(self::ESIGN_API_FLOW_REVOKE, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options)->getJson();
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
     * @return array
     * @throws GuzzleException
     */
    public function flowDelay(string $flow_id, int $sign_flow_expire_time): array {
        $uri = sprintf(self::ESIGN_API_FLOW_DELAY, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', [
            'signFlowExpireTime' => $sign_flow_expire_time
        ])->getJson();
    }

    /**
     * 催签流程中签署人
     * /v3/sign-flow/{signFlowId}/urge
     *
     * @param string $flow_id
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function flowUrge(string $flow_id, array $options = []): array {
        $uri = sprintf(self::ESIGN_API_FLOW_URGE, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options)->getJson();
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
     * @return array
     * @throws GuzzleException
     */
    public function flowRescission(string $flow_id, array $rescission_initiator, array $options = []): array {
        $uri = sprintf(self::ESIGN_API_RESCISSION_URL, $flow_id);

        $params = array_merge([
            'rescissionInitiator' => $rescission_initiator
        ], $options);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params)->getJson();
    }

    /**
     * 查询签署流程详情
     * /v3/sign-flow/{signFlowId}/detail
     *
     * @param string $flow_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function queryFlowDetail(string $flow_id): array {
        $uri = sprintf(self::ESIGN_API_FLOW_DETAIL, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST')->getJson();
    }

    /**
     * 查询签署流程列表
     * /v3/sign-flow/sign-flow-list
     *
     * @param int   $page_num
     * @param int   $page_size
     * @param array $options
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function queryFlowList(int $page_num, int $page_size, array $options = []): array {
        $params = array_merge([
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        $this->validateQueryFlowList($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_FLOW_LIST, 'POST', $params)->getJson();
    }

    /**
     * 查询集成方企业名下发起的签署流程列表
     * /v3/organizations/sign-flow-list
     *
     * @param int   $page_num
     * @param int   $page_size
     * @param array $options
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function queryOrganizationFlowList(int $page_num, int $page_size, array $options = []): array {
        $params = array_merge([
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        $this->validateQueryFlowList($params);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORG_SIGN_FLOW_LIST, 'POST', $params)->getJson();
    }

    /**
     * 追加签署区
     * /v3/sign-flow/{signFlowId}/signers/sign-fields
     *
     * @param string $flow_id
     * @param array  $signers
     * @param array  $options
     *
     * @return array
     * @throws ESignRequestParameterException
     * @throws GuzzleException
     */
    public function signFieldsAdd(string $flow_id, array $signers, array $options = []): array {
        $this->validateAddSignFields($signers);

        $uri = sprintf(self::ESIGN_API_SIGN_FIELD_ADD, $flow_id);

        $params = array_merge([
            'signers' => $signers
        ], $options);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $params)->getJson();
    }

    /**
     * 删除签署区
     * /v3/sign-flow/{signFlowId}/signers/sign-fields?signFieldIds=xxx1,xxx2
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/bd27ph
     *
     * @param string $flow_id
     * @param array  $field_ids
     *
     * @return array
     * @throws GuzzleException
     */
    public function signFieldsDelete(string $flow_id, array $field_ids): array {
        $uri = sprintf(self::ESIGN_API_SIGN_FIELD_DELETE, $flow_id);

        return ESignHttpHelper::doCommHttp($uri, 'DELETE', [
            'signFieldIds' => $field_ids
        ])->getJson();
    }

    /**
     * 核验合同文件签名有效性
     * /v3/files/{fileId}/verify
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/yekrnc
     *
     * @param string $file_id
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function verifySignature(string $file_id, array $options = []): array {
        $uri = sprintf(self::ESIGN_API_CONTRACT_VERIFY, $file_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options)->getJson();
    }

    /**
     * 获取区块链存证信息
     * /v3/antchain-file-info
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/ugtag2
     *
     * @param string $flow_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function queryAntchainFileInfo(string $flow_id): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ANTCHAIN_FILE_INFO, 'POST', [
            'signFlowId' => $flow_id
        ])->getJson();
    }

    /**
     * 核验区块链存证文件
     * /v3/antchain-file-info/verify
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/benz09
     *
     * @param string $file_hash
     * @param string $ant_tx_hash
     *
     * @return array
     * @throws GuzzleException
     */
    public function verifyAntchainFileInfo(string $file_hash, string $ant_tx_hash): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ANTCHAIN_VERIFY, 'POST', [
            'fileHash' => $file_hash,
            'antTxHash' => $ant_tx_hash
        ])->getJson();
    }
}
