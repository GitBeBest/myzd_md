<?php
namespace app\models\doctor;
use app\models\base\BaseFormModel;

class DoctorBookingForm extends BaseFormModel
{
    public $doctor_id;
    public $patient_id;
    public $travel_type;
    public $detail;
    public $creator_id;
    public $creator_name;
    public $status;
    public $user_agent;
    public $options_travel_type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('doctor_id, patient_id, creator_id, status, travel_type', 'required'),
            array('patient_id, creator_id, status, travel_type', 'numerical', 'integerOnly' => true),
            array('user_agent', 'length', 'max' => 20),
            array('detail', 'length', 'max' => 1000),
            array('patient_name, creator_name', 'safe'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'doctor_id' => '医生',
            'patient_id' => '患者',
            'creator_id' => '创建者',
            'travel_type' => '就诊意向',
            'detail' => '诊疗意见',
        );
    }

    public function initModel(PatientBooking $model = null) {
        if (isset($model)) {
            $attributes = $model->getAttributes();
            $this->setAttributes($attributes, true);
            $this->scenario = $model->scenario;
        } else {
            $this->status = PatientBooking::BK_STATUS_NEW;
        }

        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsTravelType();
    }

    public function loadOptionsTravelType() {
        if (is_null($this->options_travel_type)) {
            $this->options_travel_type = StatCode::getOptionsBookingTravelType();
        }
        return $this->options_travel_type;
    }

    public function setDoctorId($v) {
        $this->doctor_id = $v;
    }
    
    public function setPatientId($v) {
        $this->patient_id = $v;
    }

    public function setCreatorId($v) {
        $this->creator_id = $v;
    }

    public function setStatusNew() {
        $this->status = PatientBooking::BK_STATUS_NEW;
    }

}
