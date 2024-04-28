<?php

/**
 * Created by IntelliJ IDEA.
 * User: Max Sky
 * Date: 2024/04/25
 * Time: 04:06
 */

namespace MaxSky\ESign;

use MaxSky\ESign\Config\ESignConfig;
use MaxSky\ESign\Exceptions\ESignConfigException;
use MaxSky\ESign\Modules\Auth\OrganizationAuth;
use MaxSky\ESign\Modules\Auth\PersonAuth;
use MaxSky\ESign\Modules\Callback\Callback;
use MaxSky\ESign\Modules\FileAndTemplate\File;
use MaxSky\ESign\Modules\FileAndTemplate\Template;
use MaxSky\ESign\Modules\Order\Order;
use MaxSky\ESign\Modules\Organization\Member;
use MaxSky\ESign\Modules\Other\AuthFlow;
use MaxSky\ESign\Modules\Seal\OrganizationSeal;
use MaxSky\ESign\Modules\Seal\PersonSeal;
use MaxSky\ESign\Modules\Sign\SignContract;
use MaxSky\ESign\Modules\Sign\SignField;
use MaxSky\ESign\Modules\Sign\SignFlow;
use MaxSky\ESign\Modules\Sign\SignVerification;

class ESignOpenAPI {

    private static $instance;
    private static $config;

    /**
     * @param ESignConfig $config
     *
     * @throws ESignConfigException
     */
    private function __construct(ESignConfig $config) {
        if (!$config->appId || !$config->appSecret) {
            throw new ESignConfigException('Required App ID or App Secret not exists, please check your config.');
        }

        self::$config = $config;
    }

    /**
     * @param ESignConfig $config
     *
     * @return ESignOpenAPI
     * @throws ESignConfigException
     */
    public static function setConfig(ESignConfig $config): ESignOpenAPI {
        if (!self::$instance) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @return OrganizationAuth
     */
    public static function organizationAuth(): OrganizationAuth {
        return new OrganizationAuth(self::$config);
    }

    /**
     * @return PersonAuth
     */
    public static function personAuth(): PersonAuth {
        return new PersonAuth(self::$config);
    }

    /**
     * @return AuthFlow
     */
    public static function authFlow(): AuthFlow {
        return new AuthFlow(self::$config);
    }

    /**
     * @return Callback
     */
    public static function callback(): Callback {
        return new Callback(self::$config);
    }

    /**
     * @return File
     */
    public static function file(): File {
        return new File(self::$config);
    }

    /**
     * @return Template
     */
    public static function template(): Template {
        return new Template(self::$config);
    }

    /**
     * @return Order
     */
    public static function order(): Order {
        return new Order(self::$config);
    }

    /**
     * @return Member
     */
    public static function organizationMember(): Member {
        return new Member(self::$config);
    }

    /**
     * @return OrganizationSeal
     */
    public static function organizationSeal(): OrganizationSeal {
        return new OrganizationSeal(self::$config);
    }

    /**
     * @return PersonSeal
     */
    public static function personSeal(): PersonSeal {
        return new PersonSeal(self::$config);
    }

    /**
     * @return SignContract
     */
    public static function signContract(): SignContract {
        return new SignContract(self::$config);
    }

    /**
     * @return SignField
     */
    public static function signField(): SignField {
        return new SignField(self::$config);
    }

    /**
     * @return SignFlow
     */
    public static function signFlow(): SignFlow {
        return new SignFlow(self::$config);
    }

    /**
     * @return SignVerification
     */
    public static function signVerification(): SignVerification {
        return new SignVerification(self::$config);
    }
}
