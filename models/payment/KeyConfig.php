<?php
namespace app\models\payment;
use app\models\base\DBKeyActiveRecord;

class KeyConfig extends DBKeyActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'key_config';
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
    }

    public function getPayConfig($id = 1) {
        //读取myzd-key数据库
        $cri = new CDbCriteria();
        $cri->addCondition('id=1');
        $config_res = $this->find($cri);
        if (!empty($config_res)) {
            if ($config_res->is_active == 1) {
                return CJSON::decode($config_res->key_config);
            } else {
                Yii::log('该配置已经关闭，请开启');
            }
        } else {
            Yii::log('该配置信息不存在');
        }
    }

}
