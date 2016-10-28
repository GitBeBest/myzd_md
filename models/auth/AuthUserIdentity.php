<?php
namespace app\models\auth;
use app\models\base\ErrorList;
use app\util\StatCode;
use yii\db\Expression;
use yii\web\IdentityInterface;
use app\models\user\User;

/**
 * Class AuthUserIdentity
 * @package app\models\auth
 *
 */
class AuthUserIdentity implements IdentityInterface {

    const AUTH_TYPE_PASSWORD = 1; // authenticate by using password.
    const AUTH_TYPE_TOKEN = 2;    // authenticate by using token.

    public $auth_type;
    private $user;  // User model.
    private $token; // AuthTokenUser.
    private $role; //User role
    public $errorCode = 0;

    public function __construct($username, $password, $authType, $role=StatCode::USER_ROLE_PATIENT) {
        $this->username = $username;
        $this->password = $password;    // used as token is action_type is 'by token'.
        $this->auth_type = $authType;
        $this->role = $role;
    }

    public function authenticate() {
        switch ($this->auth_type) {
            case self::AUTH_TYPE_PASSWORD:
                return $this->authenticatePassword();
            case self::AUTH_TYPE_TOKEN:
                return $this->authenticateToken();
            default:
                $this->errorCode = ErrorList::AUTH_UNKNOWN_TYPE;
                return false;
        }
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

    public function authenticatePassword() {
        $model = new User();
        $this->user = $model->getByUsername($this->username);
        if ($this->user === null) {
            $this->errorCode = ErrorList::AUTH_USERNAME_INVALID;
        } else if ($this->user->checkLoginPassword($this->password) === false) {
            $this->errorCode = ErrorList::AUTH_PASSWORD_INVALID; //Wrong password.
        } else {
            //$this->id = $user->getId();
            if ($this->user->getLastLoginTime() === null) {
                $lastLogin = time();
            } else {
                $lastLogin = strtotime($this->user->getLastLoginTime());
            }
            \Yii::$app->getSession()->set('lastLoginTime', $lastLogin);
            $now = new Expression("NOW()");
            $this->user->last_login_time = $now;
            $this->user->update('last_login_time');

            $this->errorCode = ErrorList::ERROR_NONE;
        }

        return !$this->errorCode;
    }

    /**
     * authenticates user by token and username.     
     */
    public function authenticateToken() {
        if($this->role == StatCode::USER_ROLE_PATIENT){
            $this->token = (new AuthTokenUser())->verifyTokenPatient($this->password, $this->username);
        }elseif($this->role == StatCode::USER_ROLE_DOCTOR){
            $this->token = (new AuthTokenUser())->verifyTokenDoctor($this->password, $this->username);
        }

        if (is_null($this->token) || $this->token->isTokenValid() === false) {
            $this->errorCode = ErrorList::AUTH_TOKEN_INVALID;
        } else {
            $this->errorCode = ErrorList::ERROR_NONE;
            $this->user = $this->token->getUser();
        }
        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function hasSuccess() {
        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function getUser() {
        return $this->user;
    }

    public function getToken() {
        return $this->token;
    }

}
