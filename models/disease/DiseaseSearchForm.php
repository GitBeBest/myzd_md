<?php
namespace app\models\disease;
use app\models\base\BaseFormModel;
use app\models\patient\PatientInfo;

class DiseaseSearchForm extends BaseFormModel {

    public $name;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ['name','required', "message" => "请输入{attribute}"],
            ['name', 'string', 'max' => 50, "message" => "请输入25个字以内的{attribute}"],
            [['name'], 'safe']
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
            'name' => '疾病名',
        );
    }
}
