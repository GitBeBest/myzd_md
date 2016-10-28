<?php
namespace app\InterfaceModels;
use app\InterfaceModels\IDoctor;

class IExpertMember extends IDoctor {

    public function initModel($model, $attributesMap = null) {
        parent::initModel($model, $attributesMap);
    }

    public function attributesMapping() {
        return parent::attributesMapping();
    }

}
