<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MobileUserIdentity
 *
 * @author Administrator
 */
namespace app\models\user;

use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Class MobileUserIdentity
 * @var $this \yii\web\IdentityInterface
 * @package app\models\user
 *
 * @property integer $errorCode
 * @property string $username
 * @property integer $role
 */
class MobileUserIdentity implements  IdentityInterface {

    const ERROR_ACCOUNT_NOT_ACTIVATED = 3;
    const ERROR_NONE=0;
    const ERROR_USERNAME_INVALID=1;
    const ERROR_PASSWORD_INVALID=2;
    const ERROR_UNKNOWN_IDENTITY=100;

    private $id;
    private $role;

    public function __construct($username, $role) {
        $this->username = $username;
        $this->role = $role;
    }

    public function authenticate() {
        $user = (new User())->getByUsernameAndRole($this->username, $this->role);
        if (isset($user) === false) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            if ($user->isActivated() === false) {
                $this->errorCode = self::ERROR_ACCOUNT_NOT_ACTIVATED; //user's account is not activated.
            } else {
                $this->id = $user->getId();
                if ($user->getLastLoginTime() === null) {
                    $lastLogin = time();
                } else {
                    $lastLogin = strtotime($user->getLastLoginTime());
                }
                \Yii::$app->getSession()->set('lastLoginTime', $lastLogin);
                $now = new Expression("NOW()");
                $user->last_login_time = $now;
                $user->update('last_login_time');

                $this->errorCode = self::ERROR_NONE;
            }
        }
        return !$this->errorCode;
    }

    public static function findIdentity($id){

    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey() === $authKey;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($v) {
        $this->id = $v;
    }

    public function getRole() {
        return $this->role;
    }

}
