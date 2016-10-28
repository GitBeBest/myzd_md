<?php
namespace app\models\base;
use BaseActiveRecord;

class KeyActiveRecord extends BaseActiveRecord {
    public function getDbConnection() {
        return \Yii::$app->db2;
    }
}
