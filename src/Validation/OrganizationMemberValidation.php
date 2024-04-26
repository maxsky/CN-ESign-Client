<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/26
 * Time: 08:29
 */

namespace MaxSky\ESign\Validation;

use MaxSky\ESign\Exceptions\ESignRequestParameterException;

trait OrganizationMemberValidation {

    /**
     * @param array $members
     *
     * @return void
     * @throws ESignRequestParameterException
     */
    private function validateAddMember(array $members) {
        if (!$members) {
            throw new ESignRequestParameterException('待添加企业成员信息不存在');
        }

        foreach ($members as $member) {
            if (!($member['psnId'] ?? null) || !($member['memberName'] ?? null)) {
                throw new ESignRequestParameterException('企业成员账号 ID 或姓名/昵称不存在');
            }
        }
    }
}
