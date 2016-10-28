<?php
namespace app\apiServices;
use app\models\doctor\Doctor;
use yii\db\Query;

class ApiViewCommonwealDoctors extends EApiViewService {

    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {
        $this->loadDoctors();
    }

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

    public function loadDoctors() {
        $doctorList = include dirname(__FILE__) . '/../config/common_weal_doctors.php';
        foreach ($doctorList as $doctorIds) {
            $models = Doctor::find()
                ->where(['date_deleted' => NULL, 'id' => $doctorIds])
                ->all();
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    }

    private function setDoctors($models) {
        $temp = array();
        /**
         * @var $model Doctor
         */
        foreach ($models as $model) {
            $data = new \stdClass();
            $data->id = $model->getId();
            $data->name = $model->fullname;
            $data->hpId = $model->hospital_id;
            $data->hpName = $model->hospital_name;
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->desc = $model->description;
            $data->hpDeptId = $model->hp_dept_id;
            $data->hpDeptName = $model->hp_dept_name;
            $data->isContracted = $model->is_contracted;
            $temp[] = $data;
        }
        $this->results->page[] = $temp;
    }

}
