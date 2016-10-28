<?php
namespace app\models;

use app\apiServices\EApiViewService;
use app\models\auth\AuthSmsVerify;
use app\models\auth\AuthTokenUser;
use app\models\auth\AuthUserIdentity;
use app\models\base\ErrorList;
use app\models\user\User;
use app\util\StatCode;

class AuthManager {

    const ERROR_TOKEN_FAILED_CREATE = 101;  //  AuthTokenUser 创建失败
    const ERROR_LOGIN_REQUIRED = 401; // 需要登录

    /*     * ** API 3.0 *** */

    public function apiSendVerifyCode($values) {
        $output = [
            'status' => EApiViewService::RESPONSE_NO,
            'errorCode' => ErrorList::BAD_REQUEST,
            'errorMsg' => 'Invalid request.'
        ];

        if (isset($values['mobile']) == false || isset($values['action_type']) == false) {
            $output['errorMsg'] = 'Wrong parameters.';
            return $output;
        }
        $mobile = $values['mobile'];
        $actionType = $values['action_type'];
        $userHostIp = isset($values['userHostIp']) ? $values['userHostIp'] : null;

        $errors = $this->sendAuthSmsVerifyCode($mobile, $actionType, $userHostIp);
        if (empty($errors)) {
            $output['status'] = 'ok';
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
        } else {
            $output['errorMsg'] = '发送失败';
        }
        return $output;
    }

    public function sendAuthSmsVerifyCode($mobile, $actionType, $userHostIp) {
        $errors = array();
        $smsVerify = $this->createAuthSmsVerify($mobile, $actionType, $userHostIp);

        if (isset($smsVerify) === false) {
            $errors[] = 'null model';
            return $errors;
        }
        if ($smsVerify->hasErrors()) {
            $errors = $smsVerify->getFirstErrors();
            return $errors;
        }

        // send sms verify code.
        $smsMgr = new SmsManager();
        $errors = $smsMgr->sendSmsVerifyCode($smsVerify->getMobile(), $smsVerify->getCode(), $smsVerify->getExpiryDuration());

        return $errors;
    }

    /**
     * returns AuthSmsVerify regardless of failure.
     * @param string $mobile
     * @param string $code
     * @param int $actionType
     * @param string $userIp
     * @return AuthSmsVerify
     */
    public function verifyAuthSmsCode($mobile, $code, $actionType, $userIp) {
        // $userIp is not used.
        $smsVerify = (new AuthSmsVerify())->getByMobileAndCodeAndActionType($mobile, $code, $actionType);
        if (is_null($smsVerify)) {
            $smsVerify = new AuthSmsVerify();
            $smsVerify->addError('code', AuthSmsVerify::getErrorMessage(AuthSmsVerify::ERROR_NOT_FOUND));
        } else {
            $smsVerify->checkValidity(true, true);
        }

        return $smsVerify;
    }

    // verify code for user register.
    public function verifyCodeForRegister($mobile, $code, $userHostIp) {
        return $this->verifyAuthSmsCode($mobile, $code, AuthSmsVerify::ACTION_USER_REGISTER, $userHostIp);
    }

    // verify code for user booking.
    public function verifyCodeForBooking($mobile, $code, $userHostIp) {
        return $this->verifyAuthSmsCode($mobile, $code, AuthSmsVerify::ACTION_BOOKING, $userHostIp);
    }

    // verify code for mobile user login.
    public function verifyCodeForMobileLogin($mobile, $code, $userHostIp) {
        return $this->verifyAuthSmsCode($mobile, $code, AuthSmsVerify::ACTION_USER_LOGIN, $userHostIp);
    }

    // verify code for mobile user passwordreset.
    public function verifyCodeForPasswordReset($mobile, $code, $userHostIp) {
        return $this->verifyAuthSmsCode($mobile, $code, AuthSmsVerify::ACTION_USER_PASSWORD_RESET, $userHostIp);
    }

    // verify code for mobile user passwordreset.
    public function verifyCodeForBank($mobile, $code, $userHostIp) {
        return $this->verifyAuthSmsCode($mobile, $code, AuthSmsVerify::ACTION_USER_BANK, $userHostIp);
    }

    /**
     * 
     * @param string $mobile
     * @param integer $actionType
     * @param string $userIp
     * @return AuthSmsVerify
     */
    public function createAuthSmsVerify($mobile, $actionType, $userIp = null) {
        $smsVerify = new AuthSmsVerify();
        $success = false;
        switch ($actionType) {
            case AuthSmsVerify::ACTION_USER_LOGIN:
                $success = $smsVerify->createSmsVerifyUserLogin($mobile, $userIp);
                break;
            case AuthSmsVerify::ACTION_USER_REGISTER:
                $success = $smsVerify->createSmsVerifyRegister($mobile, $userIp);
                break;
            case AuthSmsVerify::ACTION_USER_PASSWORD_RESET:
                $success = $smsVerify->createSmsVerifyPasswordReset($mobile, $userIp);
                break;
            case AuthSmsVerify::ACTION_BOOKING:
                $success = $smsVerify->createSmsVerifyBooking($mobile, $userIp);
                break;
            case AuthSmsVerify::ACTION_USER_BANK:
                $success = $smsVerify->createSmsVerifyBank($mobile, $userIp);
                break;
            default:
                $smsVerify->addError('action_type', 'Invalid action type');
                break;
        }

        return $smsVerify;
    }

    public function deActiveAuthSmsVerify(AuthSmsVerify $smsVerify) {
        if ($smsVerify->isActive()) {
            $smsVerify->deActivateRecord();
        }
        // TODO: log error.

        return $smsVerify;
    }

    public function deActivateAllAuthSmsVerify(AuthSmsVerify $smsVerify) {
        $smsVerify->deActivateAllRecords();
        //TODO: log error.

        return $smsVerify;
    }

    /**
     * authenticates user with $username & $password. if true, creates a new AuthTokenUser and returns the token.
     * @param string $username  username used for login
     * @param string $password  password used for login.
     * @param string $userHostIp    user's ip address.
     * @return string AuthTokenUser.token.
     */
    public function doTokenDoctorLoginByPassword($username, $password, $userHostIp = null) {
        $output = array('status' => 'no'); // default status is false.
        $authUserIdentity = $this->authenticateUserByPassword($username, $password);
        if ($authUserIdentity->isAuthenticated) {
            // username and password are correct. continue to create AuthTokenUser.
            $user = $authUserIdentity->getUser();
            $userMacAddress = null;
            $deActivateFlag = true;
            $tokenUser = $this->createTokenDoctor($user->getId(), $username, $userHostIp, $userMacAddress, $deActivateFlag);  //@2015-10-28 by Hou Zhen Chuan
            if (isset($tokenUser)) {
                $output['status'] = 'ok';
                $output['token'] = $tokenUser->getToken();
                // TODO: log.
            } else {
                $output['errorCode'] = ErrorList::ERROR_TOKEN_CREATE_FAILED;
                $output['errorMsg'] = '生成token失败!';
                // TODO: log.
            }
        } else {
            $output['errorCode'] = $authUserIdentity->errorCode;
            $output['errorMsg'] = '用户名或密码不正确';
        }
        return $output;
    }

    public function doTokenDoctorAutoLogin(User $user) {
        $userId = $user->getId();
        $username = $user->getUsername();
        $authTokenUser = (new AuthTokenUser())->getFirstActiveByUserId($userId);
        if (isset($authTokenUser) && $authTokenUser->checkExpiry() === false) {
            $authTokenUser->deActivateToken();
            // unset model.
            $authTokenUser = null;
        }
        if (is_null($authTokenUser)) {
            $userHostIp = \Yii::$app->request->getUserIP();
            $userMacAddress = null;
            $deActivateFlag = false;
            $authTokenUser = $this->createTokenDoctor($userId, $username, $userHostIp, $userMacAddress, $deActivateFlag);
        }
        return $authTokenUser;
    }

    /**
     * @param $username
     * @param $password
     * @return AuthUserIdentity
     */
    public function authenticateUserByPassword($username, $password) {
        $authUserIdentity = new AuthUserIdentity($username, $password, AuthUserIdentity::AUTH_TYPE_PASSWORD);
        $authUserIdentity->authenticate();

        return $authUserIdentity;
    }

    //验证医生端的 token信息
    public function authenticateDoctorByToken($username, $token) {
        $authUserIdentity = new AuthUserIdentity($username, $token, AuthUserIdentity::AUTH_TYPE_TOKEN, StatCode::USER_ROLE_DOCTOR);
        $authUserIdentity->authenticate();

        return $authUserIdentity;
    }

    // 医生用户： USER_ROLE_DOCTOR
    public function createTokenDoctor($userId, $username, $userHostIp, $userMacAddress = null, $deActivateFlag = true) {

        $tokenUser = new AuthTokenUser();
        $tokenUser->createTokenDoctor($userId, $username, $userHostIp, $userMacAddress);
        if ($deActivateFlag) {
            $tokenUser->deActivateAllOldTokens();
        }
        $tokenUser->save();
        return $tokenUser;
    }

    public function verifyTokenUser($token, $username) {
        
    }

    /**
     * TODO: delete this.
     * @return string
     */
    public function getUsersWithAdminAccess() {
        return 'admin';
    }

    //用户密码登陆
    public function apiTokenDoctorLoginByPaw($values) {
        $mobile = $values['username'];
        $password = $values['password'];
        $user = (new User())->getByAttributes(array('username' => $mobile, 'role' => StatCode::USER_ROLE_DOCTOR));
        if (is_null($user)) {
            // error, so return errors.
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '该用户不存在!';
            return $output;
        }
        return $this->apiTokenDoctorAutoLoginByPaw($mobile, $password);
    }

    public function apiTokenDoctorAutoLoginByPaw($mobile, $password) {
        // get user by $mobile from db.
        $user = (new User())->getByUsernameAndRole($mobile, StatCode::USER_ROLE_DOCTOR);
        if (is_null($user)) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '该用户不存在';
            return $output;
        } else {
            if ($user->password != $user->encryptPassword($password)) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::BAD_REQUEST;
                $output['errorMsg'] = '用户名或密码错误!';
                return $output;
            }
        }
        // do auto doctor user.
        $authTokenUser = $this->doTokenDoctorAutoLogin($user);
        if ($authTokenUser->hasErrors()) {
            $errors = $authTokenUser->getFirstErrors();
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = array_shift($errors);
        } else {
            $output['status'] = EApiViewService::RESPONSE_OK;
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
            $output['results'] = array('token' => $authTokenUser->getToken(), 'uid' => $user->getUid(), 'isProfile' => is_object(UserDoctorProfile::model()->getByUserId($user->getId())) ? 1 : 0);
        }
        return $output;
    }

    /**
     * doctor login by using mobile no. & verify_code.
     * @param type $mobile
     * @param type $verifyCode
     * @return string
     */
    public function apiTokenDoctorLoginByMobile($values) {
        $mobile = $values['username'];
        $verifyCode = $values['verify_code'];
        $userHostIp = $values['userHostIp'];

        if (isset($verifyCode)) {
            $authSmsVerify = $this->verifyCodeForMobileLogin($mobile, $verifyCode, $userHostIp);
            if ($authSmsVerify->isValid() === false) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::BAD_REQUEST;
                $output['errorMsg'] = $authSmsVerify->getError('code');
                return $output;
            }
        }
        $user = (new User())->getByAttributes(array('username' => $mobile, 'role' => StatCode::USER_ROLE_DOCTOR));
        if (is_null($user)) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '该用户不存在!';
            return $output;
        }
        // auto login user and return token.
        return $this->apiTokenDoctorAutoLoginByMobile($mobile);
    }

    public function apiTokenDoctorAutoLoginByMobile($mobile) {
        // get user by $mobile from db.
        $user = (new User())->getByUsernameAndRole($mobile, StatCode::USER_ROLE_DOCTOR);
        if (is_null($user)) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '该用户不存在';
            return $output;
        }
        // do auto doctor user.
        $authTokenUser = $this->doTokenDoctorAutoLogin($user);
        if ($authTokenUser->hasErrors()) {
            $errors = $authTokenUser->getFirstErrors();
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = array_shift($errors);
        } else {
            $output['status'] = EApiViewService::RESPONSE_OK;
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
            $output['results'] = array('token' => $authTokenUser->getToken(), 'uid' => $user->getUid(), 'isProfile' => is_object(UserDoctorProfile::model()->getByUserId($user->getId())) ? 1 : 0);
        }
        return $output;
    }

}
