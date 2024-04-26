<?php

namespace MaxSky\ESign\Common;

use MaxSky\ESign\Exceptions\ESignFileNotExistException;

/**
 * 工具类
 *
 * @author   澄泓
 * @date     2022/08/18 15:40
 *
 * @modifier Max Sky
 * @date     2024/04/26 2:29
 */
class ESignUtilHelper {

    /**
     * 获取 MD5
     *
     * @param string $data
     *
     * @return string
     */
    public static function getContentMd5(string $data): string {
        return base64_encode(md5($data, true));
    }

    /**
     * 获取文件 Content-MD5
     *
     * @param string $file_path
     *
     * @return string
     * @throws ESignFileNotExistException
     */
    public static function getFileContentMd5(string $file_path): string {
        if (!file_exists($file_path)) {
            throw new ESignFileNotExistException("File not exist: $file_path");
        }

        // 获取文件 MD5
        $md5file = md5_file($file_path, true);

        // 计算文件的 Content-MD5
        return base64_encode($md5file);
    }

    /**
     * 生成签名
     *
     * @param string     $app_secret
     * @param string     $method
     * @param string     $content_type
     * @param string     $content_md5
     * @param array|null $headers
     * @param string     $uri
     *
     * @return string
     */
    public static function getSignature(string $app_secret,
                                        string $method,
                                        string $content_type, string $content_md5, ?array $headers, string $uri): string {
        $string = "$method\n*/*\n$content_md5\n$content_type\n\n";

        $headerStr = self::handleHeaderForSign($headers);

        if ($headerStr) {
            $string .= "$headerStr\n";
        }

        $string .= self::handleQueryForSign($uri);

        return base64_encode(hash_hmac('sha256', $string, $app_secret, true));
    }

    /**
     * 判断是网络路径还是文件路径
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isUrl(string $url): bool {
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if ($scheme) {
            return in_array(strtolower($scheme), ['http', 'https']);
        }

        return false;
    }

    /**
     * @param array|null $headers
     *
     * @return string
     */
    private static function handleHeaderForSign(?array $headers): string {
        $string = '';

        if ($headers && ksort($headers)) {
            foreach ($headers as $key => $value) {
                $string .= "$key:$value\n";
            }
        }

        return $string;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private static function handleQueryForSign(string $uri): string {
        $parsed = parse_url($uri);

        $uri = '';

        if ($parsed['path'] ?? null) {
            $uri = $parsed['path'];

            if ($parsed['query'] ?? null) {
                $uri .= '?';

                parse_str($parsed['query'], $queryArray);

                ksort($queryArray);

                foreach ($queryArray as $key => $value) {
                    if ($value) {
                        if (is_array($value)) { // get first element if value is array
                            $value = current($value);
                        }

                        $uri .= "&$key=$value";
                    } else {
                        $uri .= "&$key";
                    }
                }

                $uri = str_replace('?&', '?', $uri); // use string replace function instead of first or last index judge ;)
            }
        }

        return $uri;
    }
}
