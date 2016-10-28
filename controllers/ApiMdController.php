<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApimdController
 *
 * @author shuming
 */

namespace app\controllers;

use \app\controllers\BaseController;

class ApiMdController extends BaseController {

    Const APPLICATION_ID = 'ASCCPE';

    public function domainWhiteList() {
        return array(
            "http://crm.dev.mingyizd.com",
            "http://crm560.mingyizd.com"
        );
    }

    public function init() {
        $domainWhiteList = $this->domainWhiteList();
        $this->setHeaderSafeDomain($domainWhiteList, null);
        header('Access-Control-Allow-Credentials:true');      // 允许携带 用户认证凭据（也就是允许客户端发送的请求携带Cookie）
        return parent::init();
    }
    
    //查看列表
    public function actionList($model) {
        $values = $_GET;
        switch ($model) {
            case 'appversion':
                $get = $_GET;
                $appMgr = new AppManager();
                $output = $appMgr->loadAppVersionJson($get);
                break;
            case 'dataversion'://数据版本号
                $output = array(
                    'status' => EApiViewService::RESPONSE_OK,
                    'errorCode' => ErrorList::ERROR_NONE,
                    'errorMsg' => 'success',
                    'results' => array(
                        'version' => '20160918',
                        'localdataUrl' => Yii::app()->createAbsoluteUrl('/apimd/localdata'),
                    )
                );
                break;
            case 'localdata'://本地需要缓存的数据
                $apiService = new ApiViewLocalData();
                $output = $apiService->loadApiViewData();
                break;
            case 'specialtopic'://发现
                $apiService = new ApiViewSpecailTopic();
                $output = $apiService->loadApiViewData();
                break;
            case 'indexannouncement'://首页公告
                $apiService = new ApiViewIndex();
                $output = $apiService->loadApiViewData();
                break;
            case 'main'://登陆成功 返回的主页信息
                $user = $this->userLoginRequired($values);
                $apiSvc = new ApiViewUserInfo($user);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'doctorinfo'://医生信息
                $user = $this->userLoginRequired($values);
                $apiSvc = new ApiViewDoctorInfo($user->id);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'orderview'://支付页面显示信息
                $apiSvc = new ApiViewSalesOrder($values['refno']);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'bookingcontractdoctor'://预约签约专家所需信息
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewBookingContractDoctor($values['patientid'], $user->id, $values['doctorid']);
                $output = $apiService->loadApiViewData();
                break;
            case 'doctordr'://医生转诊会诊信息查询
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorDr($user->id);
                $output = $apiService->loadApiViewData(true);
                break;
            case 'doctorhzview'://医生会诊页面
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorHz($user->id);
                $output = $apiService->loadApiViewData(true);
                break;
            case 'doctorzzview'://医生转诊页面
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorZz($user->id);
                $output = $apiService->loadApiViewData(true);
                break;

            case 'patientlist'://患者列表
                $user = $this->userLoginRequired($values);
                $apisvc = new ApiViewDoctorPatientList($user->id);
                $output = $apisvc->loadApiViewData(true);
                break;
            case 'serachpatients'://患者查询
                $user = $this->userLoginRequired($values);
                $api = isset($values['api']) ? $values['api'] : false;
                $apisvc = new ApiViewPatientSearch($user->id, $values['name'], $api);
                $output = $apisvc->loadApiViewData(true);
                $this->renderJsonOutput($output);
                break;
            case 'bookinglist'://我的订单
                $user = $this->userLoginRequired($values);
                if (isset($values['api']) && $values['api'] >= 2) {
                    $apisvc = new ApiViewPatientBookingListV2($user->id, $values['status']);
                    $output = $apisvc->loadApiViewData(true);
                } else {
                    $apisvc = new ApiViewDoctorPatientBookingList($user->id, $values['status']);
                    $output = $apisvc->loadApiViewData();
                }
                break;
            case 'bookingcount'://各类订单数量
                $user = $this->userLoginRequired($values);
                $apisvc = new ApiViewBookingCount($user->id);
                $output = $apisvc->loadApiViewData();
                break;
            case 'doctorbookinglist'://收到的预约
                $user = $this->userLoginRequired($values);
                $apisvc = new ApiViewPatientBookingListForDoctor($user->id);
                $output = $apisvc->loadApiViewData();
                break;
            case 'orderlist'://分批支付订单
                $apiSvc = new ApiViewPayOrders($values['bookingid'], $values['ordertype']);
                $output = $apiSvc->loadApiViewData();
                break;
            case 'citylist'://城市列表
                $city = new ApiViewState();
                $output = $city->loadApiViewData();
                break;
            case 'deptlist'://科室列表
                $apiService = new ApiViewDiseaseCategory();
                $apiService->loadDiseaseCategory();
                $output = $apiService->loadApiViewData();
                break;
            case 'contractdoctorlist'://签约医生列表
                $apiService = new ApiViewDoctorSearch($values);
                $output = $apiService->loadApiViewData();
                break;
            case 'searchbooking'://病人查询
                $user = $this->userLoginRequired($values);
                $apisvc = new ApiViewPatientBookingListV2($user->id, 0, $values['name']);
                //调用父类方法将数据返回
                $output = $apisvc->loadApiViewData(true);
                $this->renderJsonOutput($output);
                break;
            case 'bankcardlist'://我的银行卡列表
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewBankCardList($user->id);
                $output = $apiService->loadApiViewData();
                break;
            case 'searchDisease'://根据关键字查询疾病
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDisease();
                $output = $apiService->getDiseaseByName($values['name'], $values['islike']);
                $this->renderJsonOutput($output);
                break;
            case 'searchDoctor'://根据关键字查询医生
                $user = $this->userLoginRequired($values);
                $api = new ApiViewSearchDoctor($values['name'], $values['islike']);
                $output = $api->loadApiViewData();
                break;
            case 'diseaseCategoryToSub'://二级疾病类型以及相对疾病列表
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDiseaseCategory();
                $apiService->getDiseaseCategoryToSub();
                $output = $apiService->loadApiViewData();
                break;
            /*             * *************************crm调用接口**************************** */
            case 'orderunpaid':     //状态改变 新增服务费
                $wxMgr = new WeixinManager();
                $output = $wxMgr->unPaid($values['bookingid']);
                break;
//             case 'certdrdotice':     //出院小结审核结果通知
//                 $wxMgr = new WeixinManager();
//                 $output = $wxMgr->drNotice($values['bookingid']);
//                 break;
            case 'profilenotice':     //医生信息审核
                $wxMgr = new WeixinManager();
                $output = $wxMgr->profileNotice($values['userid'], $values['type']);
                break;
            case 'bookingtodoctor':     //订单关联上级医生
                $wxMgr = new WeixinManager();
                $output = $wxMgr->bookingtodoctor($values['bookingid'], $values['type']);
                break;
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            //$output = $this->encryptOutput($output);
            $this->renderJsonOutput($output);
        }
    }

    //具体信息展示页面
    public function actionView($model, $id) {
        $values = $_GET;
        switch ($model) {
            case 'patientinfo'://患者信息
                $user = $this->userLoginRequired($values);
                $apisvc = new ApiViewDoctorPatientInfo($id, $user->id);
                $output = $apisvc->loadApiViewData(true);
                break;
            case 'bookinginfo'://预约信息 
                $user = $this->userLoginRequired($values);
                $apiSvc = new ApiViewPatientBooking($id, $user->id);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'doctorbooking'://收到的预约详情
                $user = $this->userLoginRequired($values);
                $apiSvc = new ApiViewPatientBookingForDoctor($id, $user->id, $values['type']);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'orderinfo'://支付信息 id为patientbooking的id
                $apiSvc = new ApiViewBookOrder($id);
                $output = $apiSvc->loadApiViewData(true);
                break;
            case 'contractdoctor'://签约医生信息
                $apiService = new ApiViewDoctor($values['id']);
                $output = $apiService->loadApiViewData();
                break;
            case 'taskpatientda'://上传完出院小结调用
                $output = array(
                    'status' => EApiViewService::RESPONSE_OK,
                    'errorCode' => ErrorList::ERROR_NONE,
                    'errorMsg' => 'success',
                );
                $apiRequest = new ApiRequestUrl();
                $remote_url = $apiRequest->getUrlDaTask() . "?id={$id}";
                $this->send_get($remote_url);
                break;
            case 'bookingcancell'://订单取消
                $user = $this->userLoginRequired($values);
                $patientMgr = new PatientManager();
                $output = $patientMgr->apiBookingCancell($id, $user->id);
                break;
            case 'bookingoperation'://下级医生确认手术已完成
                $user = $this->userLoginRequired($values);
                $patientMgr = new PatientManager();
                $output = $patientMgr->apiOperation($id, $user->id);
                break;
            case 'bankcardinfo'://银行卡具体信息
                $user = $this->userLoginRequired($values);
                $apiSvc = new ApiViewBankCardInfo($id, $user->id);
                $output = $apiSvc->loadApiViewData();
                break;
            case 'bankdelete'://删除银行卡
                $user = $this->userLoginRequired($values);
                $userMgr = new UserManager();
                $output = $userMgr->apiBankDelete($id, $user->id);
                break;
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            //$output = $this->encryptOutput($output);
            $this->renderJsonOutput($output);
        }
    }

    //创建和更新
    public function actionCreate($model) {
        $post = $_POST;
        if (empty($_POST)) {
            $post = $this->decryptPost($this->getPostData());
        }
        $output['status'] = EApiViewService::RESPONSE_NO;
        $output['errorCode'] = ErrorList::BAD_REQUEST;
        $output['errorMsg'] = 'Wrong parameters.';
        switch ($model) {
            case 'smsverifycode':// 发送验证码
                if (isset($post['smsVerifyCode'])) {
                    $values = $post['smsVerifyCode'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiSendVerifyCode($values);
                }
                break;
            case 'userregister'://注册
                if (isset($post['userregister'])) {
                    $values = $post['userregister'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $userMgr = new UserManager();
                    $output = $userMgr->apiTokenDoctorRegister($values);
                }
                break;
            case 'userpawlogin'://手机号密码登录
                if (isset($post['userpawlogin'])) {
                    // get user ip from request.
                    $values = $post['userpawlogin'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenDoctorLoginByPaw($values);
                }
                break;
            case 'userlogin'://手机号和验证码登录
                if (isset($post['userlogin'])) {
                    $values = $post['userlogin'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenDoctorLoginByMobile($values);
                }
                break;
            case 'forgetpassword'://忘记密码
                if (isset($post['forgetpassword'])) {
                    $values = $post['forgetpassword'];
                    $userMgr = new UserManager();
                    $output = $userMgr->apiForgetPassword($values);
                }
                break;
            case 'saveprofile'://医生个人信息保存
                if (isset($post['profile'])) {
                    $values = $post['profile'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $userMgr = new UserManager();
                    $output = $userMgr->apiCreateProfile($user, $values);
                }
                break;
            case 'termdoctor'://下级医生签署协议
                if ($post['termdoctor']) {
                    $values = $post['termdoctor'];
                    $user = $this->userLoginRequired($values);
                    $doctorProfile = $user->getUserDoctorProfile();
                    if (isset($doctorProfile)) {
                        $doctorProfile->date_terms_doctor = date('Y-m-d H:i:s');
                        if ($doctorProfile->update(array('date_terms_doctor'))) {
                            $output['status'] = 'ok';
                            $output['errorCode'] = ErrorList::ERROR_NONE;
                            $output['errorMsg'] = 'success';
                        } else {
                            $output['errorMsg'] = $doctorProfile->getFirstErrors();
                        }
                    } else {
                        $output['errorMsg'] = '尚未填写个人信息!';
                    }
                }
                break;
            case 'savepatient'://创建患者
                $output = array('status' => 'no');
                if (isset($post['patient'])) {
                    $values = $post['patient'];
                    $user = $this->userLoginRequired($values);
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiSavePatient($values, $user->id);
                }
                break;
            case 'savePatientDisease'://保存患者疾病信息
                $output = array('status' => 'no');
                if (isset($post['patient'])) {
                    $values = $post['patient'];
                    $user = $this->userLoginRequired($values);
                    $patientDisease = new PatientManager();
                    $output = $patientDisease->apiSaveDiseaseByPatient($values);
                }
                break;
            case 'savepatientbooking'://创建病人订单
                if (isset($post['patientbooking'])) {
                    $values = $post['patientbooking'];
                    $values['user_agent'] = ($this->isUserAgentIOS()) ? StatCode::USER_AGENT_APP_IOS : StatCode::USER_AGENT_APP_ANDROID;
                    $user = $this->userLoginRequired($values);
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiSavePatientBooking($values, $user);
                }
                break;
            case 'savedoctoropinion'://上级医生反馈
                if (isset($post['opinion'])) {
                    $values = $post['opinion'];
                    $user = $this->userLoginRequired($values);
                    $values['userId'] = $user->id;
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiSaveDoctorOpinion($values);
                }
                break;
            case 'savedoctorzz'://保存医生转诊信息
                if (isset($post['doctorzz'])) {
                    $values = $post['doctorzz'];
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $doctorMgr = new MDDoctorManager();
                    $output = $doctorMgr->apiCreateOrUpdateDoctorZhuanzhen($values);
                    //专家签约
                    $doctorProfile = $user->getUserDoctorProfile();
                    $doctorMgr->doctorContract($doctorProfile);
                }
                break;
            case 'savedoctorhz'://保存医生会诊信息
                if (isset($post['doctorhz'])) {
                    $values = $post['doctorhz'];
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->id;
                    $doctorMgr = new MDDoctorManager();
                    $output = $doctorMgr->apiCreateOrUpdateDoctorHuizhen($values);
                    //专家签约
                    $doctorProfile = $user->getUserDoctorProfile();
                    $doctorMgr->doctorContract($doctorProfile);
                }
                break;
            case 'questionnaire'://问卷调查
                if(isset($post['questionnaire'])) {
                    $user = $this->userLoginRequired($post['questionnaire']);
                    $hz_values['user_id'] = $zz_values['user_id'] = $user->id;
                    $doctorMgr = new MDDoctorManager();
                    isset($post['questionnaire']['hz']) && $hzResult = $doctorMgr->apiCreateOrUpdateDoctorHuizhen($post['questionnaire']['hz']);
                    isset($post['questionnaire']['zz']) && $zzResult = $doctorMgr->apiCreateOrUpdateDoctorZhuanzhen($post['questionnaire']['zz']);
                    //专家签约
                    $doctorProfile = $user->getUserDoctorProfile();
                    $doctorMgr->doctorContract($doctorProfile);
                    
                    $output = new stdClass();
                    $output->status = 'ok';
                    $output->errorMsg = 'success';
                    $output->errorCode = 0;
                    
                    if ($hzResult['status'] == 'no') {
                        if (isset($hzResult['errorMsg'])) {
                            $output->status = 'no';
                            $output->errorMsg = $hzResult['errorMsg'];
                        }
                    }
                    elseif ($zzResult['status'] == 'no') {
                        if (isset($zzResult['errorMsg'])) {
                            $output->status = 'no';
                            $output->errorMsg = $zzResult['errorMsg'];
                        }
                    }
                }
                break;
            case 'notjoinzz'://不参加医生转诊
                if (isset($post['notjoinzz'])) {
                    $values = $post['notjoinzz'];
                    $user = $this->userLoginRequired($values);
                    $doctorMgr = new MDDoctorManager();
                    $output = $doctorMgr->apiDisJoinZhuanzhen($user->id);
                }
                break;
            case 'notjoinhz'://不参加医生会诊
                if (isset($post['notjoinhz'])) {
                    $values = $post['notjoinhz'];
                    $user = $this->userLoginRequired($values);
                    $doctorMgr = new MDDoctorManager();
                    $output = $doctorMgr->apiDisJoinHuizhen($user->id);
                }
                break;
            case 'banksetkey'://账户密码设置
                if (isset($post['bank'])) {
                    $values = $post['bank'];
                    $user = $this->userLoginRequired($values);
                    $user->setUserKey($values['userkey']);
                    $user->setUserKeyRaw($values['userkey']);
                    if ($user->update(array('user_key', 'user_key_raw'))) {
                        $output['status'] = EApiViewService::RESPONSE_OK;
                        $output['errorCode'] = ErrorList::ERROR_NONE;
                        $output['errorMsg'] = 'success';
                    }
                }
                break;
            case 'bankverifycode'; //账户验证码验证
                if (isset($post['bank'])) {
                    $values = $post['bank'];
                    $user = $this->userLoginRequired($values);
                    $authMgr = new AuthManager();
                    $authSmsVerify = $authMgr->verifyCodeForBank($user->getMobile(), $values['code'], null);
                    if ($authSmsVerify->isValid()) {
                        $output['status'] = EApiViewService::RESPONSE_OK;
                        $output['errorCode'] = ErrorList::ERROR_NONE;
                        $output['errorMsg'] = 'success';
                    } else {
                        $output['errorMsg'] = $authSmsVerify->getError('code');
                    }
                }
                break;
            case 'bankverifykey'://账户验证密码
                if (isset($post['bank'])) {
                    $values = $post['bank'];
                    $user = $this->userLoginRequired($values);
                    $userKey = $user->encryptUserKey($values['userkey']);
                    if ($userKey === $user->getUserKey()) {
                        $output['status'] = EApiViewService::RESPONSE_OK;
                        $output['errorCode'] = ErrorList::ERROR_NONE;
                        $output['errorMsg'] = 'success';
                    } else {
                        $output['errorMsg'] = '密码输入错误!';
                    }
                }
                break;
            case 'savebankcard'://保存或修改银行卡信息
                if (isset($post['card'])) {
                    $values = $post['card'];
                    $user = $this->userLoginRequired($values);
                    $values['user_id'] = $user->getId();
                    $userMgr = new UserManager();
                    $output = $userMgr->ApiCreateCard($values);
                }
                break;
            case 'deletedoctorpatient'://根据ids逻辑删除患者
                if (isset($post['patient'])) {
                    $values = $post['patient'];
                    $user = $this->userLoginRequired($values);
                    $values['patient_ids'] = json_decode($values['patient_ids']);
                    $apiSvc = new ApiViewDeletePatientByIds($values['patient_ids']);
                    $output = $apiSvc->loadApiViewData();
                }
                break;
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        if (empty($output)) {
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            //$output = $this->encryptOutput($output);
            $this->renderJsonOutput($output);
        }
    }

    private function userLoginRequired($values) {
        if (isset($values['username']) === false || isset($values['token']) === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '没有权限执行此操作'));
        }
        $username = $values['username'];
        $token = $values['token'];
        $authMgr = new AuthManager();
        $authUserIdentity = $authMgr->authenticateDoctorByToken($username, $token);
        if (is_null($authUserIdentity) || $authUserIdentity->isAuthenticated === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '用户名或token不正确'));
        }
        return $authUserIdentity->getUser();
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html') {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        header('Content-type: ' . $content_type);

        if ($body != '') {
            echo $body;
        } else {
            $message = '';
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=iso-8859-1">
        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
        </head>
        <body>
        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
        <p>' . $message . '</p>
        <hr />
        <address>' . $signature . '</address>
        </body>
        </html>';

            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '

        ';
    }

    public function decryptPost($json) {
        $json = urldecode($json);
        $x = CJSON::decode($json, true);
        $client = 'app';
        $rasConfig = CoreRasConfig::model()->getByClient($client);
        $publicKey = $rasConfig->public_key;
        $privateKey = $rasConfig->private_key;
        $rsa = new RsaEncrypter($publicKey, $privateKey);
        $decrypt = $rsa->newDecrypt($x);
        return CJSON::decode(base64_decode($decrypt), true);
    }

    private function getApiVersionFromRequest() {
        return Yii::app()->request->getParam("api", 3);
    }

}
