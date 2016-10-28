<?php
namespace app\InterfaceModels;
use app\InterfaceModels\ViewModel;

class IHospitalDepartment extends ViewModel {

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'hid' => 'hospital_id',
            'group' => 'group',
        );
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getHospitalId() {
        return $this->hid;
    }

}
