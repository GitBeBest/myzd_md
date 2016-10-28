<?php
namespace app\models\auth;
use app\models\base\BaseActiveRecord;
use app\util\StatCode;
use yii\db\Expression;
use yii\web\User;

/**
 * Class AuthTokenUser
 * @package app\models\auth
 *
 * @property integer $id
 * @property string $token
 * @property integer $username
 * @property integer $user_role
 * @property integer $user_id
 * @property integer $is_active
 * @property integer $time_expiry
 * @property string $user_host_ip
 * @property string $user_mac_address
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property integer $user_agent
 *
 * The followings are the available model relations:
 * @property User $user
 */
class AuthTokenUser extends BaseActiveRecord {

    const EXPIRY_DEFAULT = 31536000;    //one year
    const ERROR_NONE = 0;
    const ERROR_NOT_FOUND = 1;
    const ERROR_INACTIVE = 2;
    const ERROR_EXPIRED = 3;

    public $error_code;
    private $verified = false;  // flag indicating if token is verified.

    /**
     * @return string the associated database table name
     */

    public static function tableName() {
        return 'auth_token_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['date_created'], 'required'],
            [['user_id, is_active, time_expiry'], 'numerical', 'integerOnly' => true],
            [['token'], 'length', 'is' => 32],
            [['user_host_ip'], 'length', 'max' => 15],
            [['username'], 'length', 'max' => 11],
            [['user_mac_address'], 'length', 'max' => 50],
            [['date_updated, date_deleted'], 'safe'],
            [['id, token, user_id, username, is_active, time_expiry, user_host_ip, user_mac_address, date_created, date_updated, date_deleted'], 'safe', 'on' => 'search']
        ];
    }

    /**
     * userRelation
     * @return \yii\db\ActiveQuery
     */
    public function getAtuUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'token' => 'Token',
            'user_id' => 'User',
            'is_active' => 'Is Active',
            'time_expiry' => 'Time Expiry',
            'user_host_ip' => 'User Host Ip',
            'user_mac_address' => 'User Mac Address',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function initModel($userId, $username, $userRole, $userHostIp, $userMacAddress) {
        $this->createToken($userId, $username, $userRole, $userHostIp, $userMacAddress);
    }

    // 创建 token。
    public function createTokenPatient($userId, $username, $userHostIp, $userMacAddress) {
        $this->createToken($userId, $username, StatCode::USER_ROLE_PATIENT, $userHostIp, $userMacAddress);
    }

    public function createTokenDoctor($userId, $username, $userHostIp, $userMacAddress) {
        $this->createToken($userId, $username, StatCode::USER_ROLE_DOCTOR, $userHostIp, $userMacAddress);
    }

    public function createToken($userId, $username, $userRole, $userHostIp, $userMacAddress) {
        $this->setUserId($userId);
        $this->setUsername($username);
        $this->setUserRole($userRole);
        $this->setToken();
        $this->setTimeExpiry();
        $this->setUserHostIp($userHostIp);
        $this->setUserMacAddress($userMacAddress);
        $this->setIsActive(true);
    }

    // 。
    public function verifyTokenPatient($token, $username) {
        return $this->verifyByTokenAndUsernameAndRole($token, $username, StatCode::USER_ROLE_PATIENT);
    }

    public function verifyTokenDoctor($token, $username) {
        return $this->verifyByTokenAndUsernameAndRole($token, $username, StatCode::USER_ROLE_DOCTOR);
    }

    /**
     * @param $token
     * @param $username
     * @param $userRole
     * @return $this|null
     */
    public function verifyByTokenAndUsernameAndRole($token, $username, $userRole) {
        $model = $this->getByTokenAndUsernameAndRole($token, $username, $userRole, true);
        if (isset($model)) {
            $model->verifyToken();
            return $model;
        } else {
            return null;
        }
    }

    //@不用了。 - 2015-10-28 by QP
    public function verifyByTokenAndUsername($token, $username) {
        $model = $this->getByTokenAndUsername($token, $username, true);
        if (isset($model)) {
            $model->verifyToken();
            return $model;
        } else {
            return null;
        }
    }

    public function verifyToken() {
        if ($this->checkExpiry()) {
            $this->error_code = self::ERROR_NONE;
        } else {
            $this->error_code = self::ERROR_EXPIRED;
        }
        $this->verified = true;
    }

    public function isTokenValid() {
        return ($this->verified && $this->error_code === self::ERROR_NONE);
    }

    public function deActivateToken() {
        $this->setIsActive(false);
        $this->date_updated = new Expression("NOW()");
        return $this->update(array('is_active', 'date_updated'));
    }

    public function deActivateAllOldTokens() {
        $now = new Expression("NOW()");
        return $this->updateAllByAttributes(array('is_active' => 0, 'date_updated' => $now), array('user_id' => $this->user_id, 'is_active' => '1'));
    }

    public function checkExpiry() {
        if (is_null($this->time_expiry)) {
            return true;
        } else {
            $now = time();
            return ($this->time_expiry > $now);
        }
    }

    /**
     * Max length supported is 62.
     * @param int $length
     * @return string
     */
    private function strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));

        return ($ret);
    }

    private function getByTokenAndUsernameAndRole($token, $username, $userRole, $isActiveFlag = true) {
        $isActive = $isActiveFlag === true ? 1 : 0;
        $model = $this->getByAttributes(array('token' => $token, 'username' => $username, 'user_role' => $userRole, 'is_active' => $isActive));
        if (isset($model)) {
            return $model;
        }
        return null;
    }

    //@不用了。 参照 getByTokenAndUsernameAndRole(). - 2015-10-28 by QP
    private function getByTokenAndUsername($token, $username, $isActiveFlag = true) {
        $isActive = $isActiveFlag === true ? 1 : 0;
        $model = $this->getByAttributes(array('token' => $token, 'username' => $username, 'is_active' => $isActive));
        if (isset($model)) {
            return $model;
        }
        return null;
    }

    /*     * ****** Query Methods ******* */

    public function getAllActiveByUserId($userId) {
        $now = time();
        $result = $this->find()
            ->where(['date_deleted' => null, 'user_id' => $userId, 'is_active' => 1])
            ->andWhere(['>', 'time_expiry', $now])
            ->all();
        return $result;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function getFirstActiveByUserId($userId) {
        $result = $this->find()->where(['date_deleted' => null, 'user_id' => $userId, 'is_active' => 1])->one();
        return $result;
    }

    public function getToken() {
        return $this->token;
    }

    private function setToken() {
        $this->token = strtoupper(substr(str_shuffle(MD5(microtime())), 0, 32));   // refer to helper.php
    }

    public function getUserId() {
        return $this->user_id;
    }

    private function setUserId($v) {
        $this->user_id = $v;
    }

    public function getUsername() {
        return $this->username;
    }

    private function setUsername($v) {
        $this->username = $v;
    }

    public function getTimeExpiry() {
        return $this->time_expiry;
    }

    private function setTimeExpiry() {
        $duration = self::EXPIRY_DEFAULT;
        $now = time();
        $this->time_expiry = $now + $duration;
    }

    public function getUserHostIp() {
        return $this->user_host_ip;
    }

    private function setUserHostIp($v) {
        $this->user_host_ip = $v;
    }

    public function getUserMacAddress() {
        return $this->user_mac_address;
    }

    private function setUserMacAddress($v) {
        $this->user_mac_address = $v;
    }

    private function setIsActive($v) {
        $this->is_active = $v === true ? 1 : 0;
    }

    private function setUserRole($v) {
        $this->user_role = $v;
    }

}
