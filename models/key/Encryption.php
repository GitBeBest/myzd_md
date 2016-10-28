<?php
namespace app\models\hospital;
use app\models\base\KeyActiveRecord;

class Encryption extends KeyActiveRecord {


    public static function tableName() {
        return 'encryption';
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

  
}
