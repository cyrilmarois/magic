<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 11:36
 */

namespace app\components;

use app\models\User;
use Yii;

class XWebUser extends \yii\web\User
{
    const NO_ERROR = 0;
    const UNKNOWN_USER_ERROR = 1;
    const USER_STATUS_INACTIF = 2;
    const USER_PASSWORD_ERROR = 3;
    const USER_FACEBOOK_ERROR = 4;

    /**
     * manage user authentication
     *
     * @param $userEmail
     * @param $userPassword
     *
     * @return int
     */
    public static function authenticate($userEmail, $userPassword)
    {
        $user = User::findByUserEmail($userEmail);
        if ($user === null) {
            $status = self::UNKNOWN_USER_ERROR;
        } elseif ($user->userStatus === 0) {
            $status = self::USER_STATUS_INACTIF;
        } elseif ($user->validatePassword($userPassword, $user->userPassword) === false) {
            $status = self::USER_PASSWORD_ERROR;
        } else {
            $status = self::NO_ERROR;
        }
        return $status;
    }

    /**
     * manage user authentication
     *
     * @param $userEmail
     * @param $userAuthKey
     *
     * @return int
     */
    public static function facebookAuthenticate($userEmail, $userAuthKey)
    {
        $user = User::findByUserEmail($userEmail);
        if ($user === null) {
            $status = self::UNKNOWN_USER_ERROR;
        } elseif ($user->userStatus === 0) {
            $status = self::USER_STATUS_INACTIF;
        } elseif ($user->validateAuthKey($userAuthKey) === false) {
            $status = self::USER_FACEBOOK_ERROR;
        } else {
            $status = self::NO_ERROR;
        }
        return $status;
    }
}