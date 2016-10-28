<?php
namespace app\models\doctor;
use app\models\base\BaseFormModel;

class DoctorSearchForm extends BaseFormModel {

    public $name;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name'], 'required', "message" => "请输入{attribute}"],
            [['name'], 'string', 'max' => 50, "message" => "请输入25个字以内的{attribute}"]
        ];
    }

    public function initModel(Doctor $model = null) {
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
            'name' => '医生名',
        );
    }
}
