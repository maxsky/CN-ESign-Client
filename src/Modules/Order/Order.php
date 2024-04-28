<?php

namespace MaxSky\ESign\Modules\Order;

use MaxSky\ESign\Common\ESignHttpHelper;
use MaxSky\ESign\Common\ESignResponse;
use MaxSky\ESign\Exceptions\ESignResponseException;
use MaxSky\ESign\Modules\BaseModule;

/**
 * 套餐服务 API
 *
 * @author    婉兮
 * @date      2022/09/02 9:51
 *
 * @modifier  Max Sky
 * @date      2024/04/26 9:04
 */
class Order extends BaseModule {

    const ESIGN_API_ORDER_PLACE_URL = '/v3/orders/org-place-order-url';
    const ESIGN_API_ORDER_REMAINING = '/v3/orders/remaining-quantity';
    const ESIGN_API_ORDER_LIST = '/v3/orders/order-list';
    const ESIGN_API_ORDER_MANAGE_URL = '/v3/orders/org-order-manage-url';
    const ESIGN_API_ORDER_LICENSE = '/v1/mix/license/query';

    /**
     * 获取购买 e签宝 套餐链接
     * /v3/orders/org-place-order-url
     *
     * @url https://open.esign.cn/doc/opendoc/order3/ncbgcr
     *
     * @param string $org_id
     * @param string $transactor_psn_id
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function getPlaceOrderUrl(string $org_id,
                                     string $transactor_psn_id, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'transactorPsnId' => $transactor_psn_id
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORDER_PLACE_URL, 'POST', $params);
    }

    /**
     * 查询套餐订单列表（页面版）
     * /v3/orders/org-order-manage-url
     *
     * @url https://open.esign.cn/doc/opendoc/order3/ynxq2o
     *
     * @param string $org_id
     * @param string $transactor_psn_id
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryOrderManageUrl(string $org_id,
                                        string $transactor_psn_id, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'transactorPsnId' => $transactor_psn_id,
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORDER_MANAGE_URL, 'POST', $params);
    }

    /**
     * 查询 e签宝 套餐余量
     * /v3/orders/remaining-quantity
     *
     * @url https://open.esign.cn/doc/opendoc/order3/fdx011
     *
     * @param string $org_id
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryRemainingQuantity(string $org_id, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORDER_REMAINING, 'GET', $params);
    }

    /**
     * 查询套餐订单列表
     * /v3/orders/order-list
     *
     * @url https://open.esign.cn/doc/opendoc/order3/ozl166
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     * @param array  $options
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryOrderList(string $org_id,
                                   int    $page_num = 1, int $page_size = 20, array $options = []): ESignResponse {
        $params = array_merge([
            'orgId' => $org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ], $options);

        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORDER_LIST, 'GET', $params);
    }

    /**
     * 查询 e 签宝套餐 License
     * /v1/mix/license/query
     *
     * @url https://open.esign.cn/doc/opendoc/order3/pfr8wu
     *
     * @param string $org_id
     * @param int    $page_num
     * @param int    $page_size
     *
     * @return ESignResponse
     * @throws ESignResponseException
     */
    public function queryOrderLicense(string $org_id,
                                      int    $page_num = 1, int $page_size = 20): ESignResponse {
        return ESignHttpHelper::doCommHttp(self::ESIGN_API_ORDER_LICENSE, 'POST', [
            'orgId' => $org_id,
            'pageNum' => $page_num,
            'pageSize' => $page_size
        ]);
    }
}
