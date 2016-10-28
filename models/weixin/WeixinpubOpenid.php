<?php

/**
 * This is the model class for table "weixinpub_openid".
 *
 * The followings are the available columns in table 'weixinpub_openid':
 * @property integer $id
 * @property string $weixinpub_id
 * @property string $open_id
 * @property integer $user_id
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\user;
use app\models\base\BaseActiveRecord;

class WeiXinPubOpenid extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'weixinpub_openid';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('weixinpub_id, open_id, user_id, date_created', 'required'),
            array('user_id', 'numerical', 'integerOnly' => true),
            array('weixinpub_id', 'length', 'max' => 20),
            array('open_id', 'length', 'max' => 40),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, weixinpub_id, open_id, user_id, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'weixinpub_id' => '微信公众号id',
            'open_id' => '微信openid',
            'user_id' => 'user.id',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function getOpenId() {
        return $this->open_id;
    }

}
