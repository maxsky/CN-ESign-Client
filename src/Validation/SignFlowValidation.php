<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/26
 * Time: 05:21
 */

namespace MaxSky\ESign\Validation;

use MaxSky\ESign\Exceptions\ESignRequestParameterException;

trait SignFlowValidation {

    /**
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateCreateByFile(array $params) {
        if ($params['docs'] ?? null) {
            foreach ($params['docs'] as $doc) {
                if (!($doc['fileId'] ?? null)) {
                    throw new ESignRequestParameterException('待签署文件 ID 不存在');
                }
            }
        }

        if (!($params['signFlowConfig'] ?? null)) {
            throw new ESignRequestParameterException('签署流程配置项不存在');
        }

        if (!($params['signFlowConfig']['signFlowTitle'] ?? null)) {
            throw new ESignRequestParameterException('签署流程主题不存在');
        }

        if ($params['signers'] ?? null) {
            foreach ($params['signers'] as $signer) {
                if (!($signer['signerType'] ?? null)) {
                    throw new ESignRequestParameterException('签署方类型不存在');
                }
            }
        }
    }

    /**
     * @param array $flow_ids
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateBatchSignUrl(array $flow_ids) {
        $count = count($flow_ids);

        if (!$count) {
            throw new ESignRequestParameterException('签署流程 ID 不存在');
        }

        if ($count > 10) {
            throw new ESignRequestParameterException('最多支持 10 个签署流程 ID');
        }
    }

    /**
     * @param array $params
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateQueryFlowList(array $params) {
        if (!($params['signFlowStartTimeFrom'] ?? null) && !($params['signFlowFinishTimeFrom'] ?? null)) {
            throw new ESignRequestParameterException('签署流程开始时间不存在，请提供发起或完结时间');
        }

        if (!($params['signFlowStartTimeTo'] ?? null) && !($params['signFlowFinishTimeTo'] ?? null)) {
            throw new ESignRequestParameterException('签署流程结束时间不存在，请提供发起或完结时间');
        }
    }

    /**
     * @param array $signers
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateAddSignFields(array $signers) {
        if (count($signers) > 300) {
            throw new ESignRequestParameterException('最多支持 300 个签署方');
        }

        foreach ($signers as $signer) {
            if (!($signer['signerType'] ?? null)) {
                throw new ESignRequestParameterException('签署方类型不存在');
            }
        }
    }
}
