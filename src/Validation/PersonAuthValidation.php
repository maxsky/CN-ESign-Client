<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/26
 * Time: 07:29
 */

namespace MaxSky\ESign\Validation;

use MaxSky\ESign\Exceptions\ESignRequestParameterException;

trait PersonAuthValidation {

    /**
     * @param string|null $psn_id
     * @param string|null $psn_account
     * @param string|null $psn_id_card_num
     * @param string|null $psn_id_card_type
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateQueryPsnIdentityInfo(?string $psn_id,
                                                  ?string $psn_account,
                                                  ?string $psn_id_card_num, ?string $psn_id_card_type) {
        if (!$psn_id && !$psn_account && !$psn_id_card_num) {
            throw new ESignRequestParameterException('个人信息查询参数不存在');
        }

        if ($psn_id_card_num && !$psn_id_card_type) {
            throw new ESignRequestParameterException('个人证件号类型不存在');
        }
    }
}
