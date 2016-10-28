<?php
namespace app\modules\mobileDoctor\controllers;
use app\apiServices\ApiViewCommonwealDoctors;
use app\apiServices\ApiViewDisease;
use app\apiServices\ApiViewDiseaseCategory;
use app\apiServices\ApiViewDoctor;
use app\apiServices\ApiViewDoctorPatientInfo;
use app\apiServices\ApiViewDoctorSearch;
use app\apiServices\ApiViewSearchDoctor;
use app\apiServices\ApiViewState;
use app\apiServices\ApiViewUserInfo;
use app\models\MDDoctorManager;
use app\models\patient\PatientBookingForm;
use app\models\PatientManager;
use app\models\user\UserDoctorMobileLoginForm;
use app\models\user\UserLoginForm;
use app\models\UserManager;
use app\modules\mobileDoctor\filter\PatientContext;
use app\modules\mobileDoctor\filter\PatientCreatorContext;
use app\modules\mobileDoctor\filter\UserContext;
use app\modules\mobileDoctor\filter\UserDoctorContext;
use app\modules\mobileDoctor\filter\UserDoctorProfileContext;
use app\modules\mobileDoctor\filter\UserDoctorVerified;
use app\util\ApiRequestUrl;
use app\util\CaptchaManage;
use app\util\StatCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;

class DoctorController extends MobiledoctorController {

    public $defaultAction = 'view';
    private $model; // Doctor model
    private $patient;   // PatientInfo model
    private $patientMR; // PatientMR model

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'register', 'ajax-register', 'mobile-login', 'forget-password', 'ajax-forget-password', 'get-captcha',
                            'valid-captcha', 'view-contract-doctors', 'ajax-login', 'view-commonweal'
                        ]
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'logout', 'change-password', 'create-patient-booking', 'ajax-contract-doctor', 'ajax-state-list', 'ajax-dept-list', 'view-doctor',
                            'add-patient', 'view', 'profile', 'ajax-profile', 'ajax-upload-cert', 'doctor-info', 'doctor-certs', 'account', 'delete-doctor-cert', 'upload-cert',
                            'update-doctor', 'to-success', 'contract', 'ajax-contract', 'send-email-for-cert', 'ajax-view-doctor-zz', 'create-doctor-zz', 'ajax-doctor-zz',
                            'ajax-view-doctor-hz', 'create-doctor-hz', 'ajax-doctor-hz', 'dr-view', 'ajax-doctor-terms', 'doctor-terms', 'ajax-join-commonweal', 'commonweal-list',
                            'user-view', 'save-patient-disease', 'search-disease', 'disease-category-to-sub', 'disease-by-category-id', 'ajax-search-doctor', 'disease-search',
                            'disease-result', 'doctor-list', 'input-doctor-info', 'add-disease', 'questionnaire', 'ajax-questionnaire', 'ajax-doctor-contract', 'upload-real-auth'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ]
            ],
            'userDoctorContext' => [
                'class' => UserDoctorContext::className(),
                'only' => ['profile','ajax-profile', 'create-patient', 'ajax-create-patient', 'create-patient-mr', 'create-booking', 'account']
            ],
            'patientContext' => [
                'class' => PatientContext::className(),
                'only' => ['create-patient-mr']
            ],
            'userDoctorProfileContext' => [
                'class' => UserDoctorProfileContext::className(),
                'only' => ['contract', 'upload-cert']
            ],
            'userDoctorVerified' => [
                'class' => UserDoctorVerified::className(),
                'only' => ['delete-doctor-cert', 'ajax-upload-cert', 'ajax-upload-cert', 'ajax-profile']
            ],
            'userContext' => [
                'class' => UserContext::className(),
                'only' => ['view-contract-doctors', 'view-commonweal']
            ]
        ];
    }

    /**
     * 用户登录
     * @param string $loginType
     * @param int $registerFlag
     * @return string
     */
    public function actionMobileLogin($loginType = 'sms', $registerFlag = 0) {
        $user = $this->getCurrentUser();
        //已登录，跳到主页
        if(isset($user)) {
            return $this->redirect(['view']);
        }

        $sms_form = new UserDoctorMobileLoginForm();
        $sms_form->role = StatCode::USER_ROLE_DOCTOR;

        $pwd_form = new UserLoginForm();
        $pwd_form->role = StatCode::USER_ROLE_DOCTOR;

        $return_url = $this->getReturnUrl(Url::to('doctor/view'));

        return $this->render('mobileLogin', [
            'model' => $sms_form,
            'pwdModel' => $pwd_form,
            'returnUrl' => $return_url,
            'loginType' => $loginType,
            'registerFlag' => $registerFlag
        ]);
    }

    /**
     * 异步登录
     */
    public function actionAjaxLogin() {
        $post = $this->decryptInput();
        $output = array('status' => 'no');
        $sms_form = $paw_form = '';

        if (isset($post['UserDoctorMobileLoginForm'])) {
            $loginType = 'sms';
            $sms_form = new UserDoctorMobileLoginForm();
            $values = $post['UserDoctorMobileLoginForm'];
            $sms_form->setAttributes($values, true);
            $sms_form->role = StatCode::USER_ROLE_DOCTOR;
            $sms_form->autoRegister = false;
            $userMgr = new UserManager();
            $isSuccess = $userMgr->mobileLogin($sms_form);
        } else if (isset($post['UserLoginForm'])) {
            $loginType = 'paw';
            $paw_form = new UserLoginForm();
            $values = $post['UserLoginForm'];
            $paw_form->setAttributes($values, true);
            $paw_form->role = StatCode::USER_ROLE_DOCTOR;
            $paw_form->rememberMe = true;
            $userMgr = new UserManager();
            $isSuccess = $userMgr->doLogin($paw_form);
        } else {
            $loginType = '';
            $output['errors'] = 'no data..';
            $isSuccess = false;
        }
        if ($isSuccess) {
            $output["status"] = "ok";
        } else {
            if ($loginType == 'sms') {
                $output['errors'] = $sms_form->getErrors();
            } else {
                $output['errors'] = $paw_form->getErrors();
            }
            $output['loginType'] = $loginType;
        }
        return $this->renderJsonOutput($output);
    }

    /**
     * 个人中心
     */
    public function actionView() {
        $user = $this->getCurrentUser();
        $svc = new ApiViewUserInfo($user);
        $output = $svc->loadApiViewData();
        return $this->render('view', array(
            'user' => $output
        ));
    }

    /**
     * 加入名医公益
     * @return string
     */
    public function actionViewCommonWeal() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $isCommonweal = false;
        $profile = false;
        if (isset($doctorProfile)) {
            $profile = true;
            $isCommonweal = $doctorProfile->isCommonweal();
        }
        $apiService = new ApiViewCommonwealDoctors();
        $output = $apiService->loadApiViewData();
        return $this->render("view-commonweal", array(
            'data' => $output,
            'profile' => $profile,
            'isCommonweal' => $isCommonweal,
        ));
    }

    /**
     * 公益列表
     */
    public function actionCommonwealList() {
        $apiService = new ApiViewCommonwealDoctors();
        $output = $apiService->loadApiViewData();
        return $this->render("commonweal-list", [
            'data' => $output
        ]);
    }

    /**
     * 签约医生列表
     */
    public function actionViewContractDoctors() {
        return $this->render('view-contract-doctors');
    }

    /**
     * 获取签约医生
     */
    public function actionAjaxContractDoctor() {
        $values = $_GET;
        $values['api'] = 3;
        $apiService = new ApiViewDoctorSearch($values);
        $output = $apiService->loadApiViewData();
        return $this->renderJsonOutput($output);
    }

    /**
     * 获取城市列表
     */
    public function actionAjaxStateList() {
        $city = new ApiViewState();
        $output = $city->loadApiViewData();
        return $this->renderJsonOutput($output);
    }

    /**
     * 获取科室分类
     */
    public function actionAjaxDeptList() {
        $apiService = new ApiViewDiseaseCategory();
        $apiService->loadDiseaseCategory();
        $output = $apiService->loadApiViewData();
        return $this->renderJsonOutput($output);
    }


    /**
     * 获取医生信息
     * @param $id
     * @return string
     */
    public function actionViewDoctor($id) {
        $apiService = new ApiViewDoctor($id);
        $output = $apiService->loadApiViewData();
        return $this->render("view-doctor", array(
            'data' => $output
        ));
    }


    /**
     * 添加患者
     * @param $id
     * @return string
     */
    public function actionAddPatient($id) {
        $apiService = new ApiViewDoctor($id);
        $doctor = $apiService->loadApiViewData();
        //查看患者列表
        $userId = $this->getCurrentUserId();

        $patientInfo = null;
        if (isset($_GET['patientId'])) {
            //根据id获得患者信息
            $patientInfoApiSvc = new ApiViewDoctorPatientInfo((int)$_GET['patientId'], $userId);
            //调用父类方法将数据返回
            $patientInfo = $patientInfoApiSvc->loadApiViewData();
        }

        $form = new PatientBookingForm();
        return $this->render("add-patient", array(
            'doctorInfo' => $doctor,
            'patientInfo' => $patientInfo,
            'model' => $form
        ));
    }

    /**
     * doctorView
     */
    public function actionDrView() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $isContracted = empty($doctorProfile->date_contracted) ? false : true;
        if($isContracted === false) {
           return  $this->redirect('contract');
        }

        return $this->render("dr-view");
    }

    /**
     * 进入医生问卷调查页面
     */
    public function actionContract() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $isContracted = empty($doctorProfile->date_contracted) ? false : true;

        return $this->render("contract", array('isContracted' => $isContracted));
    }

    /**
     * 成为签约医生
     */
    public function actionAjaxDoctorContract()
    {
        $output = new \stdClass();
        $output->status = 'no';
        $output->errorMsg = '';
        $output->errorCode = '500';

        $doctorMgr = new MDDoctorManager();
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $result = $doctorMgr->doctorContract($doctorProfile);

        if ($result === true) {
            $output->status = 'ok';
            $output->errorMsg = 'success';
            $output->errorCode = 0;
        }
        elseif ($result == 2) {
            $output->errorMsg = '已经签约过了';
        }

        return $this->renderJsonOutput($output);
    }

    /**
     * 添加患者病例
     */
    public function actionAddDisease()
    {
        $session = \Yii::$app->getSession();
        return $this->render('add-disease', array(
            'id' => $session->get('addPatientId'),
            'returnUrl' => $session->get('mobileDoctor_patientCreate_returnUrl')
        ));
    }

    /**
     * 进入专家协议页面
     */
    public function actionDoctorTerms() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $teamDoctor = 0;
        if (isset($doctorProfile)) {
            if ($doctorProfile->isTermsDoctor()) {
                $teamDoctor = 1;
            }
        }
        $returnUrl = $this->getReturnUrl(Url::to('view'));
        return $this->render("doctor-terms", [
            'teamDoctor' => $teamDoctor,
            'returnUrl' => $returnUrl
        ]);
    }
    /**
     * 获取验证码
     */
    public function actionGetCaptcha() {
        $captcha = new CaptchaManage();
        return $captcha->showImg();
    }

    /**
     * 病例搜索页
     */
    public function actionDiseaseSearch()
    {
        return $this->render('disease-search');
    }

    /**
     * 二级疾病类型列表
     */
    public function actionDiseaseCategoryToSub()
    {
        $apiService = new ApiViewDiseaseCategory();
        $apiService->getDiseaseCategoryToSub();
        $output = $apiService->loadApiViewData();
        return $this->renderJsonOutput($output);
    }

    /**
     * 根据类型id获得疾病列表
     * @param int $category_id
     */
    public function actionDiseaseByCategoryId($category_id)
    {
        $apiService = new ApiViewDisease();
        $apiService->getDiseaseByCategoryId($category_id);
        $output = $apiService->loadApiViewData();
        return $this->renderJsonOutput($output);
    }

    /**
     * 根据关键字查询疾病
     * @param $name
     * @param $is_like
     */
    public function actionSearchDisease($name, $is_like)
    {
        $apiService = new ApiViewDisease();
        $output = $apiService->getDiseaseByName($name, $is_like);
        return $this->renderJsonOutput($output);
    }

    /**
     * 病例搜索结果页
     */
    public function actionDiseaseResult()
    {
        return $this->render('disease-result');
    }

    /**
     * 保存患者疾病信息
     */
    public function actionSavePatientDisease()
    {
        $output = array('status' => 'no');
        if (isset($_POST['patient'])) {
            $values = $_POST['patient'];
            $patientDisease = new PatientManager();
            $output = $patientDisease->apiSaveDiseaseByPatient($values);
        } else {
            $output['errors'] = 'miss data...';
        }

        return $this->renderJsonOutput($output);
    }

    /**
     * 医生搜索列表页
     */
    public function actionDoctorList()
    {
        return $this->render('doctor-list');
    }

    /**
     * 医生搜索
     * @param $name
     * @param $is_like
     */
    public function actionAjaxSearchDoctor($name, $is_like)
    {
        $api = new ApiViewSearchDoctor($name, $is_like);
        $output = $api->loadApiViewData();
        return $this->renderJsonOutput($output);
    }
}
