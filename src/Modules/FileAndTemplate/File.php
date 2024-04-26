<?php

namespace MaxSky\ESign\Modules\FileAndTemplate;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignUtilHelper;
use MaxSky\ESign\Constants\ContentType;
use MaxSky\ESign\Exceptions\ESignFileNotExistException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 文件服务 API
 *
 * @author  陌上
 * @date    2022/09/02 9:51
 */
class File extends BaseModule {

    const ESIGN_API_FILE_UPLOAD_URL = '/v3/files/file-upload-url';

    /**
     * @param string $file_path
     * @param string $filename
     * @param array  $options
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws GuzzleException
     */
    public function fileUploadUrl(string $file_path, string $filename, array $options = []): ?string {
        return $this->baseFileUpload(ContentType::PDF, $file_path, $filename, $options);
    }

    /**
     * @param string $file_path
     * @param string $content_md5
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws GuzzleException
     */
    public function fileUploadToHtmlUrl(string $file_path, string $content_md5): ?string {
        return $this->baseFileUpload(ContentType::STREAM, $file_path, $content_md5);
    }

    /**
     * @param string $content_type
     * @param string $file_path
     * @param string $filename
     * @param array  $options
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws GuzzleException
     */
    private function baseFileUpload(string $content_type,
                                    string $file_path, string $filename, array $options = []): ?string {
        $params = array_merge([
            'contentMd5' => ESignUtilHelper::getFileContentMd5($file_path),
            'contentType' => $content_type,
            'fileName' => $filename,
            'fileSize' => filesize($file_path)
        ], $options);

        $response = ESignHttpHelper::doCommHttp(self::ESIGN_API_FILE_UPLOAD_URL, 'POST', $params);

        if ($response->getStatus() === 200) {
            $responseJson = $response->getJson();

            // 获取文件上传地址
            $fileUploadUrl = $responseJson['data']['fileUploadUrl'] ?? null;

            if ($fileUploadUrl) {
                $fileId = $responseJson()['data']['fileId'];

                // 文件流 PUT 上传
                $response = ESignHttpHelper::uploadFileHttp($fileUploadUrl, $file_path, $content_type);

                if ($response->getStatus() === 200) {
                    return $fileId;
                }
            }
        }

        return null;
    }
}
