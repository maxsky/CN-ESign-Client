<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/29
 * Time: 05:47
 */

namespace MaxSky\ESign\Modules\Sign;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 合同签署服务 API - 核验已签文件
 */
class SignVerification extends BaseModule {

    const ESIGN_API_SIGN_VERIFY_ANTCHAIN_FILE_INFO = '/v3/antchain-file-info';
    const ESIGN_API_SIGN_VERIFY_ANTCHAIN = '/v3/antchain-file-info/verify';
    const ESIGN_API_SIGN_VERIFY_CONTRACT = '/v3/files/%s/verify';

    /**
     * 获取区块链存证信息
     * /v3/antchain-file-info
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/ugtag2
     *
     * @param string $flow_id
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryAntchainFileInfo(string $flow_id): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_SIGN_VERIFY_ANTCHAIN_FILE_INFO, 'POST', [
            'signFlowId' => $flow_id
        ]);
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
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function verifyAntchainFileInfo(string $file_hash, string $ant_tx_hash): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_SIGN_VERIFY_ANTCHAIN, 'POST', [
            'fileHash' => $file_hash,
            'antTxHash' => $ant_tx_hash
        ]);
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
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function verifyFileSignature(string $file_id, array $options = []): ESignResponse {
        $uri = sprintf(self::ESIGN_API_SIGN_VERIFY_CONTRACT, $file_id);

        return ESignHttpHelper::doCommHttp($uri, 'POST', $options);
    }
}
