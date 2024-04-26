<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/26
 * Time: 07:15
 */

namespace MaxSky\ESign\Validation;

use MaxSky\ESign\Exceptions\ESignRequestParameterException;

trait OrganizationAuthValidation {

    /**
     * @param string|null $org_id
     * @param string|null $org_name
     * @param string|null $org_id_card_num
     * @param string|null $org_id_card_type
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateQueryOrgIdentityInfo(?string $org_id,
                                                  ?string $org_name,
                                                  ?string $org_id_card_num, ?string $org_id_card_type) {
        if (!$org_id && !$org_name && !$org_id_card_num) {
            throw new ESignRequestParameterException('机构信息查询参数不存在');
        }

        if ($org_id_card_num && !$org_id_card_type) {
            throw new ESignRequestParameterException('组织机构证件类型不存在');
        }
    }
}
