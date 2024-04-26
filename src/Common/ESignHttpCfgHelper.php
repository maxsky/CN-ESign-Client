<?php

namespace MaxSky\ESign\Common;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 * HTTP 请求配置工具类
 *
 * @author   澄泓
 * @date     2022/08/18 14:27
 *
 * @modifier Max Sky
 * @date     2024/04/25 4:35
 */
class ESignHttpCfgHelper {

    public static $timeout = 20; // seconds
    public static $connectTimeout = 10; // seconds
    public static $uploadTimeout = 90; // seconds
    public static $uploadConnectTimeout = 60; // seconds
    public static $enableHttpProxy = false;
    public static $httpProxyIp;
    public static $httpProxyPort;
    public static $httpProxyUsername;
    public static $httpProxyPassword;

    /**
     * @param string            $method
     * @param string            $url
     * @param array             $headers
     * @param array|string|null $params
     *
     * @return ESignResponse
     * @throws GuzzleException
     */
    public static function sendHttp(string $method, string $url, array $headers = [], $params = null): ESignResponse {
        $httpClient = new Client();

        if ($params && is_array($params)) {
            $params = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $options = self::getOptions([
            RequestOptions::HEADERS => $headers,
        ]);

        if (in_array($method, ['GET', 'DELETE'])) {
            $options[RequestOptions::QUERY] = $params;
        } else {
            $options[RequestOptions::BODY] = $params;
        }

        $request = $httpClient->request($method, $url, $options);

        return new ESignResponse($request->getStatusCode(), $request->getBody());
    }

    /**
     * 上传文件
     *
     * @param string $url
     * @param string $content_md5
     * @param string $file_contents
     * @param string $content_type
     *
     * @return ESignResponse
     * @throws GuzzleException
     */
    public static function uploadFile(string $url, string $content_md5, string $file_contents, string $content_type): ESignResponse {
        $httpClient = new Client([
            'defaults' => [
                'config' => [
                    'curl' => [
                        CURLOPT_FILETIME => true,
                        CURLOPT_FRESH_CONNECT => false
                    ]
                ]
            ]
        ]);

        $options = self::getOptions([
            RequestOptions::TIMEOUT => self::$uploadTimeout,
            RequestOptions::CONNECT_TIMEOUT => self::$uploadConnectTimeout,
            RequestOptions::HEADERS => [
                'Content-Type' => $content_type,
                'Content-Md5' => $content_md5
            ],
            RequestOptions::BODY => $file_contents
        ]);

        $request = $httpClient->put($url, $options);

        return new ESignResponse($request->getStatusCode(), $request->getBody());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private static function getOptions(array $options = []): array {
        $options = array_merge([
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::TIMEOUT => self::$timeout,
            RequestOptions::CONNECT_TIMEOUT => self::$connectTimeout,
            RequestOptions::VERIFY => false,
        ], $options);

        self::handleProxyConfig($options);

        return $options;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    private static function handleProxyConfig(array &$options) {
        if (self::$enableHttpProxy) {
            if (self::$httpProxyUsername && self::$httpProxyPassword) {
                $proxyUri = 'http://' . self::$httpProxyUsername . ':' . self::$httpProxyPassword . '@' . self::$httpProxyIp . ':' . self::$httpProxyPort;
            } else {
                $proxyUri = 'http://' . self::$httpProxyIp . ':' . self::$httpProxyPort;
            }

            $options[RequestOptions::PROXY] = [
                'http' => $proxyUri,
                'https' => $proxyUri
            ];
        }
    }
}
