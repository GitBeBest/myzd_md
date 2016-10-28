<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\patient\PatientInfo;
use app\models\patient\PatientBooking;
use app\models\PatientManager;

class ApiViewDoctorPatientList extends EApiViewService {

    private $creatorId;  // User.id
    private $patientMgr;
    private $hasBookingList;  // array
    private $noBookingList;  //array
    private $pageSize;
    private $page;

    /**
     * 初始化类的时候将参数注入
     * ApiViewDoctorPatientList constructor.
     * @param null $creatorId
     * @param int $pageSize
     * @param int $page
     */
    public function __construct($creatorId, $pageSize = 100, $page = 1) {
        parent::__construct();
        $this->creatorId = $creatorId;
        $this->pageSize = $pageSize;
        $this->page = $page;
        $this->patientMgr = new PatientManager();
        $this->hasBookingList = array();
        $this->notBookingList = array();
    }

    protected function loadData() {
        $this->loadPatientList();
    }

    /**
     * 返回的参数
     */
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

    /**
     * 调用model层方法
     */
    private function loadPatientList() {
        $attributes = null;
        $with = array('patientBookings');
        $options = array('limit' => $this->pageSize, 'offset' => (($this->page - 1) * $this->pageSize), 'order' => 't.date_updated DESC');
        $models = $this->patientMgr->loadPatientInfoListByCreatorId($this->creatorId, $attributes, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setPatientList($models);
        } else {
            $this->hasBookingList = null;
            $this->noBookingList = null;
        }
        $this->results->hasBookingList = $this->hasBookingList;
        $this->results->noBookingList = $this->noBookingList;
    }

    //查询总数
    public function loadCount() {
        return $this->patientMgr->loadPatientCount($this->creatorId);
    }

    /**
     * 查询到的数据过滤
     * @param array $models
     * @var PatientBooking $booking
     */
    private function setPatientList(array $models) {
        /**
         * @var PatientInfo $model
         */
        foreach ($models as $model) {
            $data = new \stdClass();
            $data->id = $model->id;
            $data->name = $model->name;
            $data->age = $model->getAge();
            $data->ageMonth = $model->getAgeMonth();
            $data->cityName = $model->getCityName();
            $data->gender = $model->getGender();
            $data->mobile = $model->getMobile();
            $data->diseaseName = $model->getDiseaseName();
            $data->dateUpdated = $model->getPatientCreated();
            $booking = $model->patientBookings;
            if (arrayNotEmpty($booking)) {
                $data->bookingId = $booking[0]->getId();
                $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/bookinginfo/' . $data->bookingId);
                $this->hasBookingList[] = $data;
            } else {
                $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/patientinfo/' . $model->getId());
                $this->noBookingList[] = $data;
            }
        }
    }

    /**
     * @param PatientBooking $model
     * @return \stdClass
     */
    private function setPatientBooking(PatientBooking $model) {
        $data = new \stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->creatorId = $model->getCreatorId();
        $data->status = $model->getStatus(false);
        $data->statusCode = $model->getStatus();
        $data->travelType = $model->getTravelType();
        $data->dateStart = $model->getDateStart();
        $data->dateEnd = $model->getDateEnd();
        $data->detail = $model->getDetail(false);
        $data->apptDate = $model->getApptDate();
        $data->dateConfirm = $model->getDateConfirm();
        $data->remark = $model->getRemark(false);
        $data->dateCreated = $model->getDateCreated();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $data->dateNow = date('Y-m-d H:i', time());
        return $data;
    }

}
