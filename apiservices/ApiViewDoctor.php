<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\doctor\Doctor;

class ApiViewDoctor extends EApiViewService {

    private $doctor_id;
    private $doctor;

    public function __construct($id) {
        parent::__construct();
        $this->doctor_id = $id;
        $this->doctor = null;
    }

    protected function loadData() {
        $this->loadDoctor();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                "errorMsg" => "success",
                'results' => $this->results,
            );
        }
    }

    private function loadDoctor() {
        $doctor = (new Doctor())->getById($this->doctor_id);
        if (isset($doctor)) {
            $this->setDoctor($doctor);
        }
        $this->results->doctor = $this->doctor;
    }

    /**
     * @param Doctor $model
     */
    private function setDoctor(Doctor $model) {
        $data = new \stdClass();
        $data->id = $model->id;
        $data->name = $model->fullname;
        $data->hospitalName = $model->hospital_name;
        $data->mTitle = $model->medical_title;
        $data->aTitle = $model->academic_title;
        $data->imageUrl = $model->getAbsUrlAvatar();
        $data->hpDeptName = $model->hp_dept_name;
        $data->isContracted = $model->is_contracted;
        $data->description = $model->description;
        $data->careerExp = $model->career_exp;
        $data->honour = $model->honour;
        $data->reasons = $model->getReasons();
        $this->doctor = $data;
    }

}
