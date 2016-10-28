<?php
namespace app\util;
use yii\web\IdentityInterface;
use yii\web\User;
use app\models\user\UserIdentity;

class BaseWebUser extends User {

    private $_keyPrefix;

    public function login(IdentityInterface $identity, $duration = 0) {
        \Yii::$app->getSession()->set('role', $identity->getRole());
        parent::login($identity, $duration);
    }

    public function getRole() {
        return \Yii::$app->getSession()->get('role');
    }

    public function getId()
    {
        return \Yii::$app->getSession()->get('__id');
    }

    public function isPatient() {
        $role = $this->getRole();
        return $role == \app\models\user\User::ROLE_PATIENT;
    }

    public function isDoctor() {
        $role = $this->getRole();
        return $role == \app\models\user\User::ROLE_DOCTOR;
    }

    private function setState($key, $value, $defaultValue=null) {

    }

    private function getStateKeyPrefix()
    {
        if($this->_keyPrefix!==null)
            return $this->_keyPrefix;
        else
            return $this->_keyPrefix=md5('Yii2.'.get_class($this).'.'.'__id');
    }

    private function getState($key, $defaultValue = null) {
        $key=$this->getStateKeyPrefix().$key;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }
}
