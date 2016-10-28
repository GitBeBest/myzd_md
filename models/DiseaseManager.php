<?php
namespace app\models;
use app\models\base\ErrorList;
use app\models\disease\Disease;
use app\models\disease\DiseaseCategory;
use app\models\disease\DiseaseSearchForm;
use yii\db\Query;

class DiseaseManager
{
    const APP_VERSION = 8;
    
    public function loadDiseaseById($id, $with = null) {
        return (new Disease())->getById($id, $with);
    }

    /**
     * @return DiseaseCategory DiseaseCategory
     */
    public function loadDiseaseCategoryList() {
        $models = (new DiseaseCategory())->getAllByInCondition('t.app_version', self::APP_VERSION);
        return $models;
    }
    
    public function getDiseaseByCategoryId($catId)
    {
        $query = new Query();
        $result = $query->select(['id', 'name', 'category_id as categoryId'])
            ->from(Disease::tableName())
            ->where(['app_version' => self::APP_VERSION, 'category_id' => $catId])
            ->all();
        return $result;
    }


    public function getDiseaseByName($name, $is_like = 0)
    {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $form = new DiseaseSearchForm();
        $form->setAttributes(array('name' => $name), true);
        if ($form->validate()) {
            $data = $form->getSafeAttributes();
            $query = new Query();
            $query->select(['disease.id', 'cdj.is_common as isCommon', 'name', 'dc.sub_cat_id as subCatId', 'dc.sub_cat_name as subCatName'])
                ->from([Disease::tableName(), 'category_disease_join as cdj', 'disease_category as dc'])
                ->where(['and', 'cdj.sub_cat_id = dc.sub_cat_id', 'cdj.disease_id = disease.id', 'disease.app_version ='. self::APP_VERSION]);

            if($is_like == 1) {
                $query->andWhere(['like', 'disease.name', $data['name']]);
            } else {
                $query->andWhere(['disease.name' => $data['name']]);
            }
            $result = $query->all();
            $output['status'] = 'ok';
            $output['errorCode'] = 'success';
            $output['results'] = $result;
        }
        else {
            $output['errorMsg'] = $form->getFirstErrors();
        }
        
        return $output;
    }
}
