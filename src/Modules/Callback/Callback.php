<?php

namespace MaxSky\ESign\Modules\Callback;

use MaxSky\ESign\Exceptions\ESignCallbackException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 接收各类 E签宝 回调
 *
 * @author    婉兮
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/26 7:55
 */
class Callback extends BaseModule {

    /**
     * 签署回调
     *
     * @param string $method
     * @param array  $server_headers
     * @param array  $query
     * @param string $contents
     *
     * @return bool
     * @throws ESignCallbackException
     */
    public function verify(string $method, array $server_headers, array $query, string $contents): bool {
        if (strtoupper($method) !== 'POST') {
            throw new ESignCallbackException('非法回调');
        }

        $sign = $server_headers['HTTP_X_TSIGN_OPEN_SIGNATURE'] ?? null;

        // 校验签名 如果header里放入的值为X_TSIGN_OPEN_SIGNATURE，到header里会自动加上HTTP_，并且转化为大写，取值时如下
        if (!$sign) {
            throw new ESignCallbackException('签名不能为空');
        }

        $timestamp = $server_headers['HTTP_X_TSIGN_OPEN_TIMESTAMP'] ?? null;

        // 1. 获取时间戳的字节流
        if (!$timestamp) {
            throw new ESignCallbackException('时间戳不能为空');
        }

        // 2. 获取 query 请求，对 query 排序后按照 value1 + value2 方式拼接
        if (!$query) {
            ksort($query);
        }

        $requestQuery = implode('', array_values($query));

        // 3. 组装数据并计算签名
        $signed = hash_hmac('sha256', "$timestamp$requestQuery$contents", $this->appSecret);

        return $sign === $signed; // SIGN_MISSION_COMPLETE 签署方-签署结果通知；SIGN_FLOW_COMPLETE 流程结束通知
    }
}
