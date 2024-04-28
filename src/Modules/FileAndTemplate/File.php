<?php

namespace MaxSky\ESign\Modules\FileAndTemplate;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignUtilHelper;
use MaxSky\ESign\Constants\ContentType;
use MaxSky\ESign\Exceptions\ESignFileNotExistException;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 文件服务 API
 *
 * @author   陌上
 * @date     2022/09/02 9:51
 *
 * @modifier Max Sky
 * @date     2024/04/26 3:12
 */
class File extends BaseModule {

    const ESIGN_API_FILE_UPLOAD_URL = '/v3/files/file-upload-url';
    const ESIGN_API_FILE_STATUS = '/v3/files/%s';

    /**
     * 上传本地文件，返回文件 ID
     *
     * @param string $file_path
     * @param string $filename
     * @param array  $options
     *
     * @return string|null 文件 ID（fileId）
     * @throws ESignFileNotExistException
     * @throws ESignResponseException
     */
    public function fileUpload(string $file_path, string $filename, array $options = []): ?string {
        return $this->baseFileUpload(ContentType::PDF, $file_path, $filename, $options);
    }

    /**
     * @param string $file_path
     * @param string $content_md5
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws ESignResponseException
     */
    public function fileUploadToHtml(string $file_path, string $content_md5): ?string {
        return $this->baseFileUpload(ContentType::STREAM, $file_path, $content_md5);
    }

    /**
     * 查询文件上传状态
     * /v3/files/{fileId}
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/qz4aip
     *
     * @param string $file_id
     * @param array  $options
     *
     * @return array|null
     * @throws ESignResponseException
     */
    public function queryUploadStatus(string $file_id, array $options = []): ?array {
        $params = array_merge([
            'fileId' => $file_id
        ], $options);

        return ESignHttpHelper::doCommHttp(sprintf(self::ESIGN_API_FILE_STATUS, $file_id), 'GET', $params)->getData();
    }

    /**
     * @param string $content_type
     * @param string $file_path
     * @param string $filename
     * @param array  $options
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws ESignResponseException
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
            $responseData = $response->getData();

            // 获取文件上传地址
            $fileUploadUrl = $responseData['fileUploadUrl'] ?? null;

            if ($fileUploadUrl) {
                $fileId = $responseData['fileId'];

                // 文件流 PUT 上传
                $response = ESignHttpHelper::uploadFileHttp($fileUploadUrl, $file_path, $content_type)->getJson();

                if (!($response['errCode'] ?? -1)) {
                    return $fileId;
                }
            }
        }

        return null;
    }
}
