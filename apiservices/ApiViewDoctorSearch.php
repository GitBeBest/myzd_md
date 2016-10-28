<?php
namespace app\apiServices;

use app\models\doctor\DoctorSearch;
use app\models\doctor\Doctor;

class ApiViewDoctorSearch extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 12;
    private $doctorSearch;  // DoctorSearch model.
    private $doctors;
    private $hospital;
    private $doctorCount;     // count no. of Doctors.
    private $api = false;

    public function __construct($searchInputs) {
        parent::__construct();
        isset($searchInputs['api']) && $this->api = $searchInputs['api'];
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['page_size'] = isset($searchInputs['page_size']) && $searchInputs['page_size'] > 0 ? $searchInputs['page_size'] : $this->pageSize;
        $this->doctorSearch = new DoctorSearch($this->searchInputs);
        $this->doctorSearch->addSearchCondition('date_deleted', NULL);
    }

    protected function loadData() {
        $this->loadDoctors();
        if ($this->getCount) {
            $this->loadDoctorCount();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new \stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->dataNum = $this->doctorCount;
            $this->output->errorMsg = 'success';
            if ($this->api == 3) {
                $this->output->results = new \stdClass();
                $this->output->results->doctors = $this->doctors;
                $this->output->results->hospital = $this->hospital;
            }
            else {
                $this->output->results = $this->doctors;
            }
        }
    }

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $models = $this->doctorSearch->search();
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    }

    /**
     * @param array $models 医生
     */
    private function setDoctors(array $models) {
        $hospital = array();
        /**
         * @var $model Doctor
         */
        foreach ($models as $model) {
            $hospital[$model->hospital_id] = $model->hospital_name;
            
            $data = new \stdClass();
            $data->id = $model->id;
            $data->name = $model->name;
            $data->mTitle = $model->medical_title;
            $data->aTitle = $model->academic_title;
            $data->hpId = $model->hospital_id;
            $data->hpName = $model->hospital_name;
            $data->hpDeptName = $model->hp_dept_name;
            $data->desc = $model->description;
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->isContracted = $model->is_contracted;
            $data->reasons = $model->getReasons();
            $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/contractdoctor/' . $model->getId());
            $this->doctors[] = $data;
        }
        ksort($hospital);
        
        foreach ($hospital as $id => $h) {
            $data = new \stdClass();
            $data->id = $id;
            $data->name = $h;
            $this->hospital[] = $data;
        }
    }

    private function loadDoctorCount() {
        if (is_null($this->doctorCount)) {
            $count = $this->doctorSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->doctorCount = $count;
    }

}
