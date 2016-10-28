<?php

/**
 * This is the model class for table "core_access".
 *
 * The followings are the available columns in table 'core_access':
 * @property integer $id
 * @property string $user_host_ip
 * @property string $url
 * @property string $url_referrer
 * @property string $user_agent
 * @property string $user_host
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\base;
use app\models\base\BaseActiveRecord;

/**
 * Class CoreAccess
 * @package app\models\base
 *
 * @property integer $id
 * @property string $user_host_ip
 * @property string $url
 * @property string $url_referrer
 * @property string $user_agent
 * @property string $user_host
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class CoreAccess extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'core_access';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
       return [
           ['user_host_ip', 'string', 'max' => 20, 'min' => 20],
           [['id', 'user_host_ip', 'url', 'url_referrer', 'user_agent', 'user_host', 'date_created', 'date_updated', 'date_deleted'], 'safe', 'on' => 'search']
       ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_host_ip' => 'User Host Ip',
            'url' => 'Url',
            'url_referrer' => 'Url Referrer',
            'user_agent' => 'User Agent',
            'user_host' => 'User Host',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }
}
