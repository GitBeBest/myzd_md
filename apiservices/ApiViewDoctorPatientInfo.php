<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\patient\PatientInfo;
use app\models\patient\PatientBooking;
use app\models\PatientManager;

class ApiViewDoctorPatientInfo extends EApiViewService {

    private $id;
    private $creatorId;  // User.id
    private $patientMgr;
    private $patientInfo;  // array
    private $patientBooking;

    //初始化类的时候将参数注入

    public function __construct($id, $creatorId) {
        parent::__construct();
        $this->creatorId = $creatorId;
        $this->id = $id;
        $this->patientMgr = new PatientManager();
    }

    protected function loadData() {
        $this->loadPatientInfo();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    //调用model层方法
    private function loadPatientInfo() {
        $attributes = null;
        $with = array('patientBookings');
        $options = null;
        $model = $this->patientMgr->loadPatientInfoByIdAndCreatorId($this->id, $this->creatorId, $attributes, $with, $options);

        if (isset($model)) {
            $this->setPatientInfo($model);
            $booking = $model->getPatientBookings();
            if (arrayNotEmpty($booking)) {
                $this->setPatientBooking($booking[0]);
            } else {
                $this->patientBooking = null;
            }
            $this->results->patientBooking = $this->patientBooking;
        }
    }

    /**
     * 查询到的数据过滤
     * @param PatientInfo $model
     */
    private function setPatientInfo(PatientInfo $model) {
        $data = new \stdClass();
        $data->id = $model->id;
        $data->name = $model->name;
        $data->age = $model->age;
        $data->ageMonth = $model->getAgeMonth();
        $data->cityName = $model->getCityName();
        $data->gender = $model->getGender();
        $data->mobile = $model->getMobile();
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $this->patientInfo = $data;
        $this->results->patientInfo = $this->patientInfo;
    }

    /**
     * @param PatientBooking $model
     */
    private function setPatientBooking(PatientBooking $model) {
        $data = new \stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->creatorId = $model->getCreatorId();
        $data->status = $model->getStatus();
        $data->statusCode = $model->getStatus(false);
        $data->travelType = $model->getTravelType();
        $data->doctorName = $model->getDoctorName();
        $data->expectedDoctor = $model->getExpectedDoctor();
        $data->dateStart = $model->getDateStart();
        $data->dateEnd = $model->getDateEnd();
        $data->detail = $model->getDetail(false);
        $data->apptDate = $model->getApptDate();
        $data->dateConfirm = $model->getDateConfirm();
        $data->remark = $model->getRemark(false);
        $data->dateCreated = $model->getDateCreated();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $data->dateNow = date('Y-m-d H:i', time());
        $this->patientBooking = $data;
    }

}
