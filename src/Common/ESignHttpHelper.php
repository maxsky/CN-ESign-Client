<?php

namespace MaxSky\ESign\Common;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Config\ESignConfig;
use MaxSky\ESign\Constants\ContentType;
use MaxSky\ESign\Constants\RequestHost;
use MaxSky\ESign\Exceptions\ESignFileNotExistException;
use MaxSky\ESign\Exceptions\ESignResponseException;

/**
 * ESignHttp 请求类
 *
 * @author   澄泓
 * @date     2022/08/18 15:10
 *
 * @modifier Max Sky
 * @date     2024/04/26 2:17
 */
class ESignHttpHelper {

    private static $instance;

    private static $host;
    private static $appId;
    private static $appSecret;

    private static $customHeaders;

    private static $debug = false;

    private static $responseClass = ESignResponse::class;

    private function __construct(ESignConfig $config) {
        self::$host = $config->sandbox ? RequestHost::ESIGN_HOST_SIMULATION : RequestHost::ESIGN_HOST_FORMAL;
        self::$appId = $config->appId;
        self::$appSecret = $config->appSecret;

        self::$customHeaders = $config->customHeaders;

        self::$debug = $config->debug;

        ESignHttpCfgHelper::$timeout = $config->reqTimeout;
        ESignHttpCfgHelper::$connectTimeout = $config->reqConnectTimeout;
        ESignHttpCfgHelper::$uploadTimeout = $config->reqUploadTimeout;
        ESignHttpCfgHelper::$uploadConnectTimeout = $config->reqUploadConnectTimeout;
        ESignHttpCfgHelper::$enableHttpProxy = $config->reqEnableHttpProxy;
        ESignHttpCfgHelper::$httpProxyIp = $config->reqHttpProxyIp;
        ESignHttpCfgHelper::$httpProxyPort = $config->reqHttpProxyPort;
        ESignHttpCfgHelper::$httpProxyUsername = $config->reqHttpProxyUsername;
        ESignHttpCfgHelper::$httpProxyPassword = $config->reqHttpProxyPassword;

        if ($config->customResponseClass && !class_exists($config->customResponseClass)) {
            self::$responseClass = $config->customResponseClass;
        }
    }

    /**
     * @param ESignConfig $config
     *
     * @return ESignHttpHelper
     */
    public static function init(ESignConfig $config): ESignHttpHelper {
        if (!self::$instance) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * 常规请求
     *
     * @param string            $uri
     * @param string            $method
     * @param array|string|null $params
     * @param array             $headers
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public static function doCommHttp(string $uri, string $method, $params = null, array $headers = []): ESignResponse {
        if (!$headers && !($params['contentType'] ?? null)) {
            $headers = self::signAndBuildSignAndJsonHeader($params, $method, $uri);
        }

        try {
            $response = ESignHttpCfgHelper::sendHttp($method, self::$host . $uri, $headers, $params);

            $response = new self::$responseClass($response->getStatusCode(), $response->getBody());
        } catch (GuzzleException $e) {
            throw new ESignResponseException($e->getMessage(), $e->getCode());
        }

        if (self::$debug) {
            ESignLogHelper::printMsg($response->getStatus());
            ESignLogHelper::printMsg($response->getBody());
        }

        return $response;
    }

    /**
     * 文件上传
     *
     * @param string $upload_url
     * @param string $file_path
     * @param string $content_type
     *
     * @return ESignResponse
     * @throws ESignFileNotExistException
     * @throws ESignResponseException
     */
    public static function uploadFileHttp(string $upload_url, string $file_path, string $content_type): ESignResponse {
        $fileContent = file_get_contents($file_path);

        $contentMd5 = ESignUtilHelper::getFileContentMd5($upload_url);

        try {
            $response = ESignHttpCfgHelper::uploadFile($upload_url, $contentMd5, $fileContent, $content_type);

            $response = new self::$responseClass($response->getStatusCode(), $response->getBody());
        } catch (GuzzleException $e) {
            throw new ESignResponseException($e->getMessage(), $e->getCode());
        }

        if (self::$debug) {
            ESignLogHelper::printMsg($response->getStatus());
            ESignLogHelper::printMsg($response->getBody());
        }

        return $response;
    }

    /**
     * 签名计算并构建一个签名鉴权 + json 数据的 ESign 请求头
     *
     * @param array|string|null $params
     * @param string            $method
     * @param string            $uri
     * @param string            $content_type
     *
     * @return array
     */
    private static function signAndBuildSignAndJsonHeader($params,
                                                          string $method,
                                                          string $uri,
                                                          string $content_type = ContentType::JSON): array {
        $contentMd5 = '';

        // GET 和 DELETE 请求不能携带 body 体
        if (in_array(strtoupper($method), ['GET', 'DELETE']) || !$params) {
            $content_type = '';
        } else {
            if (is_array($params)) {
                $params = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $contentMd5 = ESignUtilHelper::getContentMd5($params);
        }

        $signature = ESignUtilHelper::getSignature(self::$appSecret, $method, $content_type, $contentMd5, self::$customHeaders, $uri);

        // 构建基础请求头
        $headers = self::buildCommonHeaders(self::$appId, $contentMd5, $signature, $content_type);

        if (self::$debug) {
            ESignLogHelper::printMsg($headers);
        }

        return $headers;
    }


    /**
     * 构造请求头
     *
     * @param string $app_id
     * @param string $content_md5
     * @param string $signature
     * @param string $content_type
     *
     * @return array
     */
    public static function buildCommonHeaders(string $app_id,
                                              string $content_md5,
                                              string $signature, string $content_type = ContentType::JSON): array {
        $headers = [
            'Accept' => '*/*', // fixed
            'Content-Type' => $content_type,
            'X-Tsign-Open-App-Id' => $app_id,
            'X-Tsign-Open-Auth-Mode' => 'Signature', // fixed
            'X-Tsign-Open-Ca-Signature' => $signature,
            'X-Tsign-Open-Ca-Timestamp' => Carbon::now()->getTimestampMs()
        ];

        if ($content_md5) {
            $headers['Content-MD5'] = $content_md5;
        }

        return $headers;
    }
}
