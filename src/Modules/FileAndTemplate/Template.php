<?php

namespace MaxSky\ESign\Modules\FileAndTemplate;

use GuzzleHttp\Exception\GuzzleException;
use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 模板服务 API
 *
 * @author  陌上
 * @date    2022/09/02 9:51
 */
class Template extends BaseModule {

    const ESIGN_API_CREATE_BY_TEMPLATE = '/v3/files/create-by-doc-template';
    const ESIGN_API_TEMPLATE_COMPONENT = '/v3/doc-templates/%s';
    const ESIGN_API_TEMPLATE_CREATE_URL = '/v3/doc-templates/doc-template-create-url';
    const ESIGN_API_TEMPLATE_LIST = '/v3/doc-templates';
    const ESIGN_API_TEMPLATE_FILE = '/v3/files/%s';

    /**
     * 填写模板生成文件
     * /v3/files/create-by-doc-template
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/mv8a3i
     *
     * @param string $doc_template_id
     * @param string $filename
     * @param array  $components
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function createByDocTemplate(string $doc_template_id,
                                        string $filename, array $components, array $options = []): array {
        $params = array_merge([
            'docTemplateId' => $doc_template_id,
            'fileName' => $filename,
            'components' => $components
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_CREATE_BY_TEMPLATE, 'POST', $params)->getJson();
    }

    /**
     * 查询合同模板中控件详情
     * /v3/doc-templates/{docTemplateId}
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/aoq509
     *
     * @param string $doc_template_id
     *
     * @return array
     * @throws GuzzleException
     */
    public function getComponents(string $doc_template_id): array {
        $uri = sprintf(self::ESIGN_API_TEMPLATE_COMPONENT, $doc_template_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET')->getJson();
    }

    /**
     * 获取制作合同模板页面
     * /v3/doc-templates/doc-template-create-url
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/xagpot
     *
     * @param string $doc_template_name
     * @param string $file_id
     * @param int    $doc_template_type
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function getCreateUrl(string $doc_template_name,
                                 string $file_id, int $doc_template_type = 0, array $options = []): array {
        $params = array_merge([
            'docTemplateName' => $doc_template_name,
            'docTemplateType' => $doc_template_type,
            'fileId' => $file_id
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_TEMPLATE_CREATE_URL, 'POST', $params)->getJson();
    }

    /**
     * 查询合同模板列表
     * /v3/doc-templates
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/mghz1g
     *
     * @param int $page_num
     * @param int $page_size
     *
     * @return array
     * @throws GuzzleException
     */
    public function getTemplateList(int $page_num = 1, int $page_size = 20): array {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_TEMPLATE_LIST, 'GET', [
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ])->getJson();
    }

    /**
     * 查询 PDF 模板填写后文件
     * /v3/files/{fileId}
     *
     * @url https://open.esign.cn/doc/opendoc/pdf-sign3/oq5w1ug1algfeo90
     *
     * @param string $file_id
     * @param array  $options
     *
     * @return array
     * @throws GuzzleException
     */
    public function getTemplateFile(string $file_id, array $options = []): array {
        $uri = sprintf(self::ESIGN_API_TEMPLATE_FILE, $file_id);

        return ESignHttpHelper::doCommHttp($uri, 'GET', $options)->getJson();
    }
}
