<?php
namespace app\controllers;
use app\controllers\WebsiteController;
use app\models\RegionManager;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RegionController extends WebsiteController {

    public function actionLoadStates() {
        $this->headerUTF8();
        $regionMgr = new RegionManager();
        $regionStates = [];
        if (isset($_GET['id'])) {
            $regionStates = $regionMgr->getAllStatesByCountryId($_GET['id']);
        } else if (isset($_GET['code'])) {
            $regionStates = $regionMgr->getAllStatesByCountryCode($_GET['code']);
        }

        if (is_array($regionStates) && count($regionStates) > 0) {
            $listData = ArrayHelper::map($regionStates, 'id', 'name_cn');
            if (count($listData) === 1) {
                foreach ($listData as $id => $name) {
                    echo Html::tag('option', Html::encode($name), ['value' => $id]);
                }
            } else {
                echo Html::tag('option', Html::encode('省份或地区'), ['value' => '']);
                foreach ($listData as $id => $name) {
                    echo Html::tag('option', Html::encode($name), ['value' => $id]);
                }
            }
        } else {
            echo Html::tag('option', Html::encode('省份或地区'), ['value' => '']);
        }
    }

    public function actionLoadCities($state = null, $prompt = '选择城市') {
        $this->headerUTF8();
        $regionMgr = new RegionManager();
        $promptText = $prompt;
        $output = '';
        $models = null;
        if (isset($_GET['state'])) {
            $models = $regionMgr->getAllCitiesByStateId($_GET['state']);
        }

        if (is_array($models)) {
            if (count($models) == 1) {
                $listData = ArrayHelper::map($models, 'id', 'name_cn');
                foreach ($listData as $id => $name) {
                    $output .= Html::tag('option', Html::encode($name), ['value' => $id]);
                }
            } else if (count($models) > 1) {
                $listData = ArrayHelper::map($models, 'id', 'name_cn');
                $output .= Html::tag('option', Html::encode($promptText), ['value' => '']);
                foreach ($listData as $id => $name) {
                    $output .= Html::tag('option', Html::encode($name), ['value' => $id]);
                }
            }
        } else {
            $output = Html::tag('option', Html::encode($promptText), ['value' => '']);
        }
        echo $output;

        \Yii::$app->end();
    }
}
