<?php
namespace app\modules\mobileDoctor\controllers;
use app\apiServices\ApiViewBankCardList;
use app\models\AuthManager;
use app\models\doctor\DoctorBankCardForm;
use app\models\doctor\UserDoctorProfile;
use app\modules\mobileDoctor\controllers\MobileDoctorController;

class UserBankController extends MobiledoctorController {
    //进入输入密码页面
    public function actionViewInputKey() {
        $user = $this->getCurrentUser();
        if (strIsEmpty($user->getUserKey())) {
            return $this->redirect(array('view-set-key'));
        }
        return $this->render("view-input-key");
        //获取加密参数
    }

    //ajax异步验证密码
    public function actionVerifyKey() {
        $output = array("status" => "no");
        $post = $this->decryptInput();
        if (isset($post['bank'])) {
            $user = $this->getCurrentUser();
            $values = $post['bank'];
            $userKey = $user->encryptUserKey($values['userkey']);
            if ($userKey === $user->getUserKey()) {
                $output['status'] = 'ok';
            } else {
                $output['errors'] = '密码输入错误!';
            }
        } else {
            $output['errors'] = 'miss data...';
        }
        return $this->renderJsonOutput($output);
    }

    //进入设置密码页面
    public function actionViewSetKey() {
        return $this->render("view-set-key");
    }

    //用户银行账户密码设置
    public function actionAjaxSetKey() {
        $output = array("status" => "no");
        $post = $this->decryptInput();
        if (isset($post['bank'])) {
            $user = $this->getCurrentUser();
            $values = $post['bank'];
            $user->setUserKey($values['userkey']);
            $user->setUserKeyRaw($values['userkey']);
            if ($user->update(array('user_key', 'user_key_raw'))) {
                $output['status'] = 'ok';
            } else {
                $output['errors'] = $user->getErrors();
            }
        } else {
            $output['errors'] = 'miss data...';
        }
        return $this->renderJsonOutput($output);
    }


    //进入验证码确认页面
    public function actionSmsCode() {
        $user = $this->getCurrentUser();
        $this->render("smsCode", array("mobile" => $user->getMobile()));
    }

    /**
     * 异步验证验证码输入是否正确
     * @param $code
     */
    public function actionAjaxVerifyCode($code) {
        $output = array("status" => "no");
        $user = $this->getCurrentUser();
        $authMgr = new AuthManager();
        $authSmsVerify = $authMgr->verifyCodeForBank($user->getMobile(), $code, null);
        if ($authSmsVerify->isValid()) {
            $output['status'] = 'ok';
        } else {
            $output['errors'] = $authSmsVerify->getError('code');
        }
        $this->renderJsonOutput($output);
    }

    public function actionCardList() {
        $userId = $this->getCurrentUserId();
        $apiService = new ApiViewBankCardList($userId);
        $output = $apiService->loadApiViewData();
        return $this->render('card-list', array('data' => $output));
    }

    //新增
    public function actionCreate() {
        $userId = $this->getCurrentUserId();
        $userDoctorProfile = new UserDoctorProfile();
        $result = $userDoctorProfile->getByUserId($userId);

        $form = new DoctorBankCardForm();
        return $this->render('create', [
            'model' => $form,
            'name' => $result->name
        ]);
    }
}
