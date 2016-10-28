<?php
namespace app\modules\mobileDoctor\controllers;

use app\apiServices\ApiViewDoctorPatientList;
use app\apiServices\ApiViewPatientBookingList;
use app\apiServices\ApiViewPatientBookingListForDoctor;
use app\models\patient\PatientBooking;
use app\models\patient\PatientBookingForm;
use app\models\patient\PatientInfo;
use app\models\UserManager;
use app\modules\mobileDoctor\filter\PatientCreatorContext;
use app\modules\mobileDoctor\filter\UserContext;
use app\modules\mobileDoctor\filter\UserDoctorContext;
use app\util\StatCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;

class PatientBookingController extends MobiledoctorController {

    private $model; // PatientBooking model.
    private $patient;   // PatientInfo model.


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'doctor-patient-booking-list'],
                        'roles' => ['*']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'create', 'ajax-create', 'update', 'list', 'doctor-patient-booking-list', 'doctor-patient-booking',
                            'ajax-doctor-opinion', 'ajax-booking-num', 'ajax-operation', 'ajax-cancel', 'ajax-list', 'search-view', 'ajax-search'],
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
                'only' => ['create'],
            ],
            'userContext' => [
                'class' => UserContext::className(),
                'only' => ['list', 'doctor-patient-booking-list']
            ]
        ];
    }

    public function initPatient() {
        $patientId = null;
        if (isset($_GET['pid'])) {
            $patientId = $_GET['pid'];
        } elseif (isset($_GET['id'])) {
            $patientId = $_GET['id'];
        } else if (isset($_POST['patient']['id'])) {
            $patientId = $_POST['patient']['id'];
        }
        $creator_id = $this->getCurrentUserId();

        if (is_null($this->patient)) {
            $this->patient = (new PatientInfo())->getByIdAndCreatorId($patientId, $creator_id);
            if (is_null($this->patient)) {
                throw new HttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->patient;
    }
    /**
     * 查询创建者的签约信息
     */
    public function actionList() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->userDoctorProfile;
        $teamDoctor = 0;
        if (isset($doctorProfile)) {
            if ($doctorProfile->isVerified()) {
                if ($doctorProfile->isTermsDoctor() === false) {
                    $teamDoctor = 1;
                }
            }
        }
        return $this->render('booking-list', array(
            'teamDoctor' => $teamDoctor
        ));
    }

    /**
     * 订单列表
     * @param int $page
     * @param int $status
     */
    public function actionAjaxList($page = 1, $status = 0) {
        $userId = $this->getCurrentUserId();
        $api_svc = new ApiViewDoctorPatientList($userId, $status, null, 200, $page);
        //调用父类方法将数据返回
        $output = $api_svc->loadApiViewData(true);
        return $this->renderJsonOutput($output);
    }

    /**
     * 查询预约该医生的预约列表
     * @param int $page
     * @return string
     */
    public function actionDoctorPatientBookingList($page = 1) {
        $page_size = 100;
        $doctorId = $this->getCurrentUserId();
        $api_svc = new ApiViewPatientBookingListForDoctor($doctorId, $page_size, $page);
        //调用父类方法将数据返回
        $output = $api_svc->loadApiViewData();
        return $this->render('doctor-patient-booking-list', array(
            'data' => $output
        ));
    }

    /**
     * 订单创建页面
     */
    public function actionCreate() {
        $user = $this->getCurrentUser();
        $doctorProfile = $user->userDoctorProfile;
        $userMgr = new UserManager();
        $certs = $userMgr->loadUserDoctorFilesByUserId($user->id);
        $doctorCerts = 0;
        if (arrayNotEmpty($certs)) {
            $doctorCerts = 1;
        }
        $userDoctorProfile = 0;
        if (isset($doctorProfile)) {
            $userDoctorProfile = 1;
        }
        $patient = $this->initPatient();
        $form = new PatientBookingForm();
        $form->initModel();
        //判断数据来源
        if ($this->isUserAgentWeixin()) {
            $form->user_agent = StatCode::USER_AGENT_WEIXIN;
        } else {
            $form->user_agent = StatCode::USER_AGENT_MOBILE_WEB;
        }
        $form->setPatientId($patient->getId());
        return $this->render('create', array(
            'model' => $form,
            'userDoctorProfile' => $userDoctorProfile,
            'doctorCerts' => $doctorCerts
        ));
    }

    /**
     * 订单搜索页面
     */
    public function actionSearchView() {
        return $this->render('search-view');
    }

    /**
     * 查询结果集
     * @param $name
     */
    public function actionAjaxSearch($name) {
        $userId = $this->getCurrentUserId();
        $api_svc = new ApiViewPatientBookingList($userId, 0, $name);
        //调用父类方法将数据返回
        $output = $api_svc->loadApiViewData(true);
        return $this->renderJsonOutput($output);
    }
}
