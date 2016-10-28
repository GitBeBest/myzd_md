<?php

class IndustrialInfoForm extends EFormModel {

    public $id;
    public $hospital_id;
    public $hospital_name;
    public $category_id;
    public $cat_name;
    public $clinic_title;
    public $academic_title;
    public $options_c_title;
    public $options_a_title;
    public $options_subcat;
    public $options_state;
    public $options_city;
    public $country_id;

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, hospital_id, category_id, clinic_title, academic_title', 'numerical', 'integerOnly' => true),
            array('hospital_name, cat_name', 'length', 'max' => 50),
        );
    }

    public function attributeLabels() {
        return array(
            'hospital_id' => '医院id',
            'hospital_name' => '医院名',
            'category_id' => '专业id',
            'cat_name' => '专业',
            'clinic_title' => '临床职称',
            'academic_title' => '学术职称',
        );
    }

    public function initModel($model = null) {
        if (isset($model)) {
            $attributes = $model->attributes;
            $this->setAttributes($attributes, true);
        }
        $this->country_id = 1;
        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsClinicalTitle();
        $this->loadOptionsAcademicTitle();
        $this->loadOptionsSubcat();
    }

    /**
     * 临床职称
     * @return array
     */
    public function loadOptionsClinicalTitle() {
        if (is_null($this->options_c_title)) {
            $this->options_c_title = StatCode::getOptionsClinicalTitle();
        }
        return $this->options_c_title;
    }

    /**
     * 学术职称
     * @return array
     */
    public function loadOptionsAcademicTitle() {
        if (is_null($this->options_a_title)) {
            $this->options_a_title = $this->getOptionsAcademicTitle();
        }
        return $this->options_a_title;
    }
    
    private  function getOptionsAcademicTitle() {
        return array(
            StatCode::DR_A_TITLE_PROF => '教授',
            StatCode::DR_A_TITLE_PROF_ASSOC => '副教授',
            '3'=>'讲师',
            '4'=>'助教',
        );
    }

    public function loadOptionsSubcat() {
        if (is_null($this->options_subcat)) {
            $this->options_subcat = CHtml::listData(DiseaseCategory::model()->loadAllCatSub(), 'sub_cat_id', 'sub_cat_name');
        }
        return $this->options_subcat;
    }

    public function loadOptionsState() {
        if (is_null($this->options_state)) {
            $this->options_state = CHtml::listData(RegionState::model()->getAllByCountryId($this->country_id), 'id', 'name');
        }
        return $this->options_state;
    }

    public function loadOptionsCity() {
        if (is_null($this->state_id)) {
            $this->options_city = array();
        } else {
            $this->options_city = CHtml::listData(RegionCity::model()->getAllByStateId($this->state_id), 'id', 'name');
        }
        return $this->options_city;
    }

}
