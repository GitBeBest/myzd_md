<?php
namespace app\models\patient;
use app\models\base\BaseFormModel;

class PatientDiseaseForm extends BaseFormModel {

    public $id;
    public $disease_name;
    public $disease_detail;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['id', 'disease_name', 'disease_detail'], 'required'],
            [['id'], 'number', 'integerOnly' => true],
            [['disease_name'], 'string', 'max' => 50],
            [['disease_detail'], 'string', 'max' => 1000],
            [['id', 'disease_name', 'disease_detail'], 'safe']
        ];
    }

    public function initModel(PatientInfo $model = null) {
        if (isset($model)) {
            $attributes = $model->getAttributes();
            // set safe attributes.
            $this->setAttributes($attributes, true);
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病史描述',
        );
    }
}
