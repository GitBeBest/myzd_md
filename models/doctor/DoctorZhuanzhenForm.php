<?php

/**
 * 医生转诊
 *
 * @author shuming
 */
namespace app\models\doctor;
use app\models\base\BaseFormModel;

class DoctorZhuanzhenForm extends BaseFormModel {

    public $user_id;
    public $is_join;
    public $fee;
    public $preferred_patient;
    public $prep_days;

    public function rules() {
        return [
            [['user_id', 'is_join', 'fee'], 'number', 'integerOnly' => true],
            [['prep_days', 'preferred_patient'], 'string', 'max' => 500]
        ];
    }

    public function attributeLabels() {
        return array(
            'user_id' => 'User',
            'is_join' => '是否参加?',
            'fee' => '是否需要转诊费?',
            //'week_days' => '您一般一周内那天比较方便?',
            'preferred_patient'=>'对转诊病历有何要求?',
            'prep_days' => '您最快多久能安排手术?',
        );
    }

    public function initModel(UserDoctorZhuanzhen $model = null) {
        if (isset($model)) {
            $attributes = $model->attributes;
            $this->setAttributes($attributes, true);
        } else {
            //默认为不参与
            $this->is_join = 0;
        }
    }

}
