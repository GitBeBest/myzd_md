<?php
namespace app\InterfaceModels;

abstract class ViewModel {

    public function initModel($model, $attributes = null) {
        $attributesMap = $this->getAttributesMapping($attributes);

        foreach ($attributesMap as $imodelAttr => $modelAttr) {
            $this->{$imodelAttr} = $model->{$modelAttr};
        }
    }

    /**
     * gets the selected attributes mapping, defined in $attributes.
     * @param array $attributes
     * @return array attributesMapping.
     */
    public function getAttributesMapping($attributes = null) {
        if (is_null($attributes)) {
            return $this->attributesMapping();
        } else {
            $output = array();
            $mapList = $this->attributesMapping();
            foreach ($attributes as $attr) {
                if (isset($mapList[$attr])) {
                    $output[$attr] = $mapList[$attr];
                }
            }
            return $output;
        }
    }

    public function attributesMapping() {
        return array();
    }

    protected function getTextAttribute($value, $ntext = true) {
        if ($ntext) {
            return Yii::app()->format->formatNtext($value);
        } else {
            return $value;
        }
    }

}
