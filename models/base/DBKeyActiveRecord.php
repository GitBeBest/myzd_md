<?php
namespace app\models\base;
use BaseActiveRecord;

class DBKeyActiveRecord extends BaseActiveRecord {

    public function getDbConnection() {
        return \Yii::$app->db2;
    }

}
