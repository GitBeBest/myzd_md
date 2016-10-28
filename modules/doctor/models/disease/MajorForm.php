<?php
use yii\base\Model;

class MajorForm extends Model {

    public $id;
    public $diseaseList; //擅长疾病
    public $surgeryList; //擅长手术

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'numerical', 'integerOnly' => true),
        );
    }

}
