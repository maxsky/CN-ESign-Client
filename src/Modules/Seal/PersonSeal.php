<?php

namespace MaxSky\ESign\Modules\Seal;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignUtilHelper;
use MaxSky\ESign\Constants\ContentType;
use MaxSky\ESign\Exceptions\ESignFileNotExistException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 印章服务-个人API
 *
 * @author  天音
 * @date    2022/09/02 9:51
 */
class PersonSeal extends BaseModule {

    const ESIGN_API_PSN_SEAL_CREATE_BY_TEMPLATE = '/v3/seals/psn-seals/create-by-template';
    const ESIGN_API_PSN_SEAL_CREATE_BY_IMAGE = '/v3/seals/psn-seals/create-by-image';
    const ESIGN_API_PSN_SEAL_LIST = '/v3/seals/psn-seal-list';
    const ESIGN_API_PSN_SEAL = '/v3/seals/psn-seal';
    const ESIGN_API_PSN_SEAL_CREATE_URL = '/v3/seals/psn-seal-create-url';
    const ESIGN_API_PSN_SEAL_MANAGE_URL = '/v3/seals/psn-seals-manage-url';
    const ESIGN_API_PSN_SEAL_UPLOAD_IMAGE = '/v3/files/file-key';

    /**
     * 创建个人模板印章
     *
     * @param string $psn_id
     * @param string $seal_name
     * @param string $seal_template_style
     * @param string $seal_size
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function createByTemplate(string $psn_id,
                                     string $seal_name,
                                     string $seal_template_style, string $seal_size, array $options = []): array {
        $params = array_merge([
            'psnId' => $psn_id,
            'sealName' => $seal_name,
            'sealTemplateStyle' => $seal_template_style,
            'sealSize' => $seal_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_CREATE_BY_TEMPLATE, 'POST', $params)->getJson();
    }

    /**
     * 创建个人图片印章
     * /v3/seals/psn-seals/create-by-image
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/yi2wca
     *
     * @param string $psn_id
     * @param string $seal_image_file_key
     * @param string $seal_name
     * @param string $seal_width
     * @param string $seal_height
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function createByImage(string $psn_id,
                                  string $seal_image_file_key,
                                  string $seal_name, string $seal_width, string $seal_height, array $options = []): array {
        $params = array_merge([
            'psnId' => $psn_id,
            'sealImageFileKey' => $seal_image_file_key,
            'sealName' => $seal_name,
            'sealWidth' => $seal_width,
            'sealHeight' => $seal_height
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_CREATE_BY_IMAGE, 'POST', $params)->getJson();
    }

    /**
     * 查询个人印章列表
     * /v3/seals/psn-seal-list
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/wvyyt7
     *
     * @param string $psn_id
     * @param int    $page_num
     * @param int    $page_size
     *
     * @return array
     * @throws GuzzleException
     */
    public function querySealList(string $psn_id, int $page_num = 1, int $page_size = 10): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_LIST, 'GET', [
            'psnId' => $psn_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ])->getJson();
    }

    /**
     * 获取创建个人印章页面链接
     * /v3/seals/psn-seal-create-url
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/cwc95p
     *
     * @param string $psn_id
     * @param string $redirect_url
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function getCreateUrl(string $psn_id, string $redirect_url, array $options = []): array {
        $params = array_merge([
            'psnId' => $psn_id,
            'redirectUrl' => $redirect_url
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_CREATE_URL, 'POST', $params)->getJson();
    }

    /**
     * 获取管理个人印章页面链接
     * /v3/seals/psn-seals-manage-url
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/qksso1
     *
     * @param string $psn_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function getManageUrl(string $psn_id): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_MANAGE_URL, 'POST', [
            'psnId' => $psn_id
        ])->getJson();
    }

    /**
     * 上传印章图片
     * /v3/files/file-key
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/gd1tsb
     *
     * @param string $image_path
     * @param string $filename
     *
     * @return string|null
     * @throws ESignFileNotExistException
     * @throws GuzzleException
     */
    public function uploadSealImage(string $image_path, string $filename): ?string {
        $params = [
            'contentMd5' => ESignUtilHelper::getFileContentMd5($image_path),
            'contentType' => ContentType::STREAM,
            'fileName' => $filename,
            'fileSize' => filesize($image_path)
        ];

        $response = ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL_UPLOAD_IMAGE, 'POST', $params);

        if (!$response->getCode()) {
            $fileUploadUrl = $response->getJson()['data']['fileUploadUrl'] ?? null;

            if ($fileUploadUrl) {
                $fileKey = $response->getJson()['data']['fileKey'] ?? null;

                $result = ESignHttpHelper::uploadFileHttp($fileUploadUrl, $image_path, ContentType::STREAM)->getJson();

                if (!($result['errCode'] ?? -1)) {
                    return $fileKey;
                }
            }
        }

        return null;
    }

    /**
     * 删除个人印章
     * /v3/seals/psn-seal
     *
     * @url https://open.esign.cn/doc/opendoc/seal3/pnr1w7
     *
     * @param string $psn_id
     * @param string $seal_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function delete(string $psn_id, string $seal_id): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_PSN_SEAL, 'DELETE', [
            'psnId' => $psn_id,
            'sealId' => $seal_id
        ])->getJson();
    }
}
