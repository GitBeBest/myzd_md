<?php
namespace app\modules\mobileDoctor\controllers;

use app\apiServices\ApiViewDoctorPatientInfo;
use app\apiServices\ApiViewDoctorPatientList;
use app\apiServices\ApiViewPatientSearch;
use app\models\patient\PatientInfo;
use app\models\patient\PatientInfoForm;
use app\models\PatientManager;
use app\models\region\RegionCity;
use app\models\region\RegionState;
use yii\helpers\Url;

class PatientController extends MobiledoctorController {

    private $patient;

    /**
     * 填写预约单选择患者页
     * @param int $page
     * @return string
     */
    public function actionChooseList($page = 1)
    {
        $user = $this->getCurrentUser();
        $userId = $user->getId();
        $doctorProfile = $user->getUserDoctorProfile();
        $teamDoctor = 0;
        if (isset($doctorProfile)) {
            if ($doctorProfile->isVerified()) {
                if ($doctorProfile->isTermsDoctor() === false) {
                    $teamDoctor = 1;
                }
            }
        }
        $page_size = 100;
        //service层
        $apiSvc = new ApiViewDoctorPatientList($userId, $page_size, $page);
        //调用父类方法将数据返回
        $output = $apiSvc->loadApiViewData();
        
        $dataCount = $apiSvc->loadCount();
        return $this->render('choose-list', [
            'data' => $output,
            'dataCount' => $dataCount,
            'teamDoctor' => $teamDoctor
        ]);
    }

    /**
     * 我的患者列表信息查询
     * @param int $page
     * @return string
     */
    public function actionList($page = 1) {
        $user = $this->getCurrentUser();
        $userId = $user->getId();
        $doctorProfile = $user->getUserDoctorProfile();
        $teamDoctor = 0;
        if (isset($doctorProfile)) {
            if ($doctorProfile->isVerified()) {
                if ($doctorProfile->isTermsDoctor() === false) {
                    $teamDoctor = 1;
                }
            }
        }
        $page_size = 100;
        //service层
        $apiSvc = new ApiViewDoctorPatientList($userId, $page_size, $page);
        //调用父类方法将数据返回
        $output = $apiSvc->loadApiViewData();

        $dataCount = $apiSvc->loadCount();
        return $this->render('list', [
            'data' => $output,
            'dataCount' => $dataCount,
            'teamDoctor' => $teamDoctor
        ]);
    }

    /**
     * 我的患者详情
     * @param $id
     * @return string
     */
    public function actionView($id) {
        $userId = $this->getCurrentUserId();
        //service层
        $apiSvc = new ApiViewDoctorPatientInfo($id, $userId);
        //调用父类方法将数据返回
        $output = $apiSvc->loadApiViewData();
        return $this->render('view', array(
            'data' => $output,
            'user_id' => $userId
        ));
    }
    /**
     * 进入搜索页面
     */
    public function actionSearchView() {
        return $this->render('search-view');
    }

    /**
     * ajax查询
     * @param $name
     */
    public function actionAjaxSearch($name) {
        $userId = $this->getCurrentUserId();
        $apiSvc = new ApiViewPatientSearch($userId, $name, 3);
        $output = $apiSvc->loadApiViewData(true);
        return $this->renderJsonOutput($output);
    }

    /**
     * 病人信息创建界面
     */
    public function actionCreate() {
        $returnUrl = $this->getReturnUrl();
        if(!empty($returnUrl)) {
            \Yii::$app->getSession()->set('mobileDoctor_patientCreate_returnUrl', $returnUrl);
        }
        $user = $this->getCurrentUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $teamDoctor = 0;
        if (isset($doctorProfile)) {
            if ($doctorProfile->isVerified()) {
                if ($doctorProfile->isTermsDoctor() === false) {
                    $teamDoctor = 1;
                }
            }
        }
        $form = new PatientInfoForm();
        $form->initModel();
        return $this->render("create-patient", array(
            'model' => $form,
            'teamDoctor' => $teamDoctor,
            'returnUrl' => $returnUrl
        ));
    }

    /**
     * 病人用户创建
     */
    public function actionAjaxCreate() {
        $post = $this->decryptInput();
        $output = array('status' => 'no');
        if (isset($post['patient'])) {
            $values = $post['patient'];
            $form = new PatientInfoForm();
            $form->setAttributes($values, true);
            $form->creator_id = $this->getCurrentUserId();
            $form->country_id = 1;  // default country is China.
            if ($form->validate()) {
                $patientMgr = new PatientManager();
                $patient = $patientMgr->loadPatientInfoById($form->id);
                if (isset($patient) === false) {
                    $patient = new PatientInfo();
                }
                $patient->setAttributes($form->attributes, true);
                $patient->setAge();
                $regionState = (new RegionState())->getById($patient->state_id);
                $patient->state_name = $regionState->getName();
                $regionCity = (new RegionCity())->getById($patient->city_id);
                $patient->city_name = $regionCity->getName();
                if ($patient->save()) {
                    $output['status'] = 'ok';
                    $output['patient']['id'] = $patient->getId();
                    \Yii::$app->getSession()->set('addPatientId',$output['patient']['id']);
                } else {
                    $output['errors'] = $patient->getErrors();
                }
            } else {
                $output['errors'] = $form->getErrors();
            }
        } else {
            $output['error'] = 'data errors';
        }
        return $this->renderJsonOutput($output);
    }

    /**
     * 病人用户补全图片 type为是创建还是修改 返回不同的页面
     * @param $id
     * @return string
     */
    public function actionUploadMrFile($id) {
        $user = $this->getCurrentUser();
        $returnUrl = $this->getReturnUrl(Url::to('patient-booking/create'));
        $url = 'update-mr-file';
        if ($this->isUserAgentIOS()) {
            $url .= '-ios';
        } else {
            $url .= '-android';
        }
        return $this->render($url, [
            'output' => [
                'id' => $id,
                'returnUrl' => $returnUrl
            ],
            'user' => $user
        ]);
    }
}
