<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @proeprty integer $role
 * @property string $name
 * @property string $email
 * @property string $qq
 * @property string $wechat
 * @property string $login_attempts
 * @property string $password
 * @property string $salt
 * @property string $password_raw
 * @property integer $terms 
 * @property string $date_activated
 * @property string $last_login_time
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\user;
use app\models\base\BaseActiveRecord;
use app\models\doctor\UserDoctorCert;
use app\models\doctor\UserDoctorProfile;
use app\util\StatCode;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package app\models\user
 *
 * @property integer $id
 * @property integer $uid
 * @property string $username
 * @property integer $role
 * @property string $name
 * @property string $email
 * @property string $qq
 * @property string $wechat
 * @property string $password
 * @property string $user_key
 * @property integer $login_attempts
 * @property string $salt
 * @property string $password_raw
 * @property string $user_key_raw
 * @property integer $terms
 * @property string $date_activated
 * @property string $last_login_time
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class User extends BaseActiveRecord{

    const ROLE_PATIENT = 1;
    const ROLE_DOCTOR = 2;

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['username', 'password', 'role', 'salt', 'terms'], 'required'],
            [['role', 'login_attempts', 'terms'], 'number', 'integerOnly' => true],
            [['username'], 'string', 'max' => 11, 'min' => 11],
            [['name', 'qq', 'wechat'], 'string', 'max' => 45],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'max' => 64, 'min' => 64],
            [['salt'], 'string', 'min' => 40],
            [['password_raw'], 'required', 'message' => '请填写{attribute}.', 'on' => 'register'],
            [['password_raw'], 'string', 'min' => 4, 'max' => 40, 'tooShort' => '{attribute}不可少于4位.', 'tooLong' => '{attribute}不可超过40位', 'on' => 'register'],
            [['date_activated', 'last_login_time', 'date_created', 'date_updated', 'date_deleted'], 'safe'],
            [['id', 'username', 'name', 'email', 'qq', 'wechat', 'password', 'salt', 'password_raw', 'login_attempts', 'date_activated', 'last_login_time', 'date_created', 'date_updated', 'date_deleted'], 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMedicalRecords() {
        return $this->hasMany('MedicalRecord', ['id' => 'user_id']);
    }

    /**
     * @return UserDoctorProfile
     */
    public function getUserDoctorProfile() {
        return $this->hasOne(UserDoctorProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @return UserDoctorCert
     */
    public function getUserDoctorCerts() {
        return $this->hasMany(UserDoctorCert::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPatients() {
        return $this->hasMany('PatientInfo', ['id' => 'creator_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'username' => '手机号码',
            'role' =>'角色',
            'name' =>'姓名',
            'email' => '邮箱',
            'qq' => 'QQ',
            'wechat' => '微信',
            'password' => '登录密码',
            'salt' => 'Salt',
            'password_raw' => '登录密码',
            'password_repeat' => '确认密码',
            'login_attempts' => '登录尝试次数',
            'date_activated' => '激活日期',
            'last_login_time' => '最后登录时间',
            'date_created' => '创建日期',
            'date_updated' => '更新日期',
            'date_deleted' => '删除日期',
        );
    }

    /**
     * @param string $username  User.username.     
     * @return User model.
     */
    public function getByUsername($username) {
        return $this->getByAttributes(array('username' => $username));
    }

    public function getByUsernameAndRole($username, $role) {
        return $this->getByAttributes(array('username' => $username, 'role' => $role));
    }

    /*     * ****** Public Methods ****** */

    public function createNewModel() {
        $this->createSalt();
        $this->createPassword();
    }

    public function checkLoginPassword($passwordInput) {
        return ($this->password === $this->encryptPassword($passwordInput));
    }

    public function changePassword($passwordInput) {
        $this->password_raw = $passwordInput;
        $this->password = $this->encryptPassword($passwordInput);
        return $this->update(array('password', 'password_raw'));
    }

    public function checkUsernameExists($username) {
        return $this->find()->where(['username' => $username, 'date_deleted' => null])->exists();
    }

    public function isDoctor($checkVerify = true) {
        if ($this->role != StatCode::USER_ROLE_DOCTOR) {
            return false;
        } elseif ($checkVerify) {
            $userDoctorProfile = $this->getUserDoctorProfile();
            return (isset($userDoctorProfile) && $userDoctorProfile->getDateVerified(false) !== null);
        } else {
            return true;
        }
    }

    /*     * ****** Private Methods ******* */

    private function createSalt() {
        $this->salt = $this->strRandom(40);
    }

    private function createPassword() {
        $this->password = $this->encryptPassword($this->password_raw);
    }

    public function encryptPassword($password, $salt = null) {
        if ($salt === null) {

            return ($this->encrypt($password . $this->salt));
        } else {
            return ($this->encrypt($password . $salt));
        }
    }

    private function encrypt($value) {
        return hash('sha256', $value);
    }

    // Max length supported is 62.
    private function strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));

        return ($ret);
    }

    /*     * ****** Query Methods ******* */
    public function createCriteriaMedicalRecords() {
//        $criteria = new CDbCriteria();
//        $criteria->compare('t.user_id', $this->id);
//        $criteria->order = 't.date_created ASC';
//        $criteria->with = array('mrBookings');
//        return $criteria;
    }

//    public function getUserMedicalRecords() {
//        return $this->userMedicalRecords->with('mrBookings');
//    }

    public function isActivated() {
        return $this->date_activated !== null;
    }

    public function setActivated() {
        $this->date_activated = new Expression("NOW()");
    }

    public function setUserKey($v) {
        $this->user_key = $this->encrypt($v);
    }

    public function setUserKeyRaw($v) {
        $this->user_key_raw = $v;
    }
    public function getUserKey() {
        return $this->user_key;
    }
    public function encryptUserKey($v) {
        return $this->encrypt($v);
    }

    public function getLastLoginTime() {
        return $this->last_login_time;
    }

    public function getMobile() {
        return $this->username;
    }

    public function getUsername() {
        return $this->username;
    }
}
