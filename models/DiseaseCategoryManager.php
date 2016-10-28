<?php
namespace app\models;

use app\models\disease\Disease;
use app\models\disease\DiseaseCategory;
use yii\db\Expression;
use yii\db\Query;

class DiseaseCategoryManager
{
    const APP_VERSION = 8;
    
    public function getDiseaseCategoryToSub()
    {
        $query = new Query();
        $data = $query->select([
            'dc.id as dcid',
            'dc.sub_cat_id as subCatId',
            'dc.sub_cat_name as subCatName',
            'd.id as id',
            'd.name as name',
            'cdj.is_common as isCommon'
        ])->from([
            'dc' => DiseaseCategory::tableName(),
            'cdj' => 'category_disease_join',
            'd' => Disease::tableName()
        ])->where(['and', '`dc`.`sub_cat_id` =`cdj`.`sub_cat_id`', '`cdj`.`disease_id` = `d`.`id`', '`dc`.`app_version` ='.self::APP_VERSION])
        ->all();
        $result = array();
        foreach ($data as $d) {
            $std = new \stdClass();
            $std->id = $d['id'];
            $std->name = $d['name'];
            $std->isCommon = $d['isCommon'];
            if (key_exists($d['subCatId'], $result)) {
                array_push($result[$d['subCatId']]['diseaseName'], $std);
            } else {
                $result[$d['subCatId']]['id'] = $d['dcid'];
                $result[$d['subCatId']]['subCatName'] = $d['subCatName'];
                $result[$d['subCatId']]['diseaseName'] = array();
                array_push($result[$d['subCatId']]['diseaseName'], $std);
            }
        }

        return $result;
    }
}
