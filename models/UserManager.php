<?php
namespace app\models;

use app\models\doctor\DoctorBankCard;
use app\models\doctor\UserDoctorCert;
use app\models\doctor\UserDoctorProfile;
use app\models\user\MobileUserIdentity;
use app\models\user\User;
use app\models\user\UserDoctorMobileLoginForm;
use app\models\user\UserLoginForm;
use app\util\StatCode;

class UserManager {
    /**
     * 医生信息查询
     * @param $userId
     * @param null $attributes
     * @param null $with
     * @return UserDoctorProfile
     */
    public function loadUserDoctorProfileByUserId($userId, $attributes = null, $with = null) {
        return (new UserDoctorProfile())->getByUserId($userId, $attributes, $with);
    }

    /**
     * 医生文件查询
     * @param $userId
     * @param null $attributes
     * @param null $with
     * @return UserDoctorCert
     */
    public function loadUserDoctorFilesByUserId($userId, $attributes = null, $with = null) {
        return (new UserDoctorCert())->getDoctorFilesByUserId($userId, $attributes, $with);
    }

    public function loadCardsByUserId($userId) {

        return (new DoctorBankCard())->getAllByAttributes(array("user_id" => $userId));
    }
    /**
     * Login user.
     * @param UserLoginForm $form
     * @return boolean
     */
    public function doLogin(UserLoginForm $form) {
        return ($form->validate() && $form->login());
    }

    /**
     * 手机用户登录
     * @param UserDoctorMobileLoginForm $form
     * @return bool
     */
    public function mobileLogin(UserDoctorMobileLoginForm $form) {
        if ($form->validate()) {
            $form->authenticate();
            if ($form->autoRegister && $form->errorFormCode == MobileUserIdentity::ERROR_USERNAME_INVALID) {
                if ($form->role == StatCode::USER_ROLE_DOCTOR) {
                    $this->createUserDoctor($form->username);
                } elseif ($form->role == StatCode::USER_ROLE_PATIENT) {
                    $this->createUserPatient($form->username);
                }
                //之前有错误 user为null  再次验证
                $form->authenticate();
            }
            if ($form->errorFormCode == MobileUserIdentity::ERROR_NONE) {
                \Yii::$app->user->login($form->_identity, $form->duration);
                return true;
            }
        }
        return false;
    }

    /**
     * 创建医生用户
     * @param $mobile
     * @return mixed
     */
    public function createUserDoctor($mobile) {
        return $this->createUser($mobile, StatCode::USER_ROLE_DOCTOR);
    }

    /**
     * 创建病人用户
     * @param $mobile
     * @return mixed
     */
    public function createUserPatient($mobile) {
        return $this->createUser($mobile, StatCode::USER_ROLE_PATIENT);
    }

    /**
     * 创建用户
     * @param $mobile
     * @param $statCode
     * @return null|User
     */
    private function createUser($mobile, $statCode) {
        $model = new User();
        $model->scenario = 'register';
        $model->username = $mobile;
        $model->role = $statCode;
        $model->password_raw = strRandom(6);
        $model->terms = 1;
        $model->createNewModel();
        $model->setActivated();
        if ($model->save()) {
            return $model;
        }
        return null;
    }
}
