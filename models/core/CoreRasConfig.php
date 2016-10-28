<?php
namespace app\models\core;
use app\models\base\BaseActiveRecord;

/**
 * Class CoreRasConfig
 * @package app\models\core
 * @property integer $id
 * @property integer $client
 * @property integer $public_key
 * @property integer $private_key
 * @property integer $date_start
 * @property integer $date_end
 * @property integer $appt_date
 */
class CoreRasConfig extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function getDb() {
        return \Yii::$app->db2;
    }

    public static function tableName() {
        return 'encryption';
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'key_name' => 'KEY名称',
            'client' => '使用来源客户端',
            'public_key' => '公钥',
            'private_key' => '私钥',
            'date_created' => '创建日期',
            'date_updated' => '修改日期',
            'date_deleted' => '删除日期'
        );
    }

    public function getByClient($client) {
        return $this->getByAttributes(array('client' => $client));
    }

    public function getId() {
        return $this->id;
    }

    public function getClient() {
        return $this->client;
    }

    public function getPublicKey() {
        return $this->public_key;
    }

    public function getPrivateKey() {
        return $this->private_key;
    }

    public function getDateStart($format = null) {
        return $this->getDateAttribute($this->date_start, $format);
    }

    public function getDateEnd($format = null) {
        return $this->getDateAttribute($this->date_end, $format);
    }

    public function getAppDate($format = null) {
        return $this->getDatetimeAttribute($this->appt_date, $format);
    }

}
