<?php
namespace app\models\patient;
use app\models\base\BaseFormModel;
use app\models\patient\PatientBooking;
use app\util\StatCode;

/**
 * Class PatientBookingForm
 * @package app\models\patient
 */
class PatientBookingForm extends BaseFormModel {

    public $patient_id;
    public $patient_name;
    public $expected_doctor;
    public $expected_hospital;
    public $expected_dept;
    public $creator_id;
    public $creator_name;
    public $status;
    public $travel_type;
    public $detail;
    public $user_agent;
    public $remark;
    public $options_travel_type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['patient_id', 'creator_id', 'status', 'travel_type'], 'required'],
            [['patient_id', 'creator_id', 'status', 'travel_type'], 'number', 'integerOnly' => true],
            [['user_agent'], 'string', 'max' => 20],
            [['expected_doctor'], 'string', 'max' => 200],
            [['detail'], 'string', 'max' => 1000],
            [['remark'], 'string', 'max' => 500],
            [['expected_doctor', 'expected_dept', 'expected_hospital', 'patient_name', 'creator_name', 'doctor_name'], 'safe']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'patient_id' => '患者',
            'creator_id' => '创建者',
            'status' => '状态',
            'travel_type' => '出行方式',
            'expected_doctor' => '期望医生',
            'expected_hospital' => '期望医院',
            'expected_dept' => '期望科室',
            'detail' => '细节',
            'remark' => '备注',
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
