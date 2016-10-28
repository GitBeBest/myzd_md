<?php
namespace app\modules\weiXinPub\models;
use app\models\base\BaseActiveRecord;

/**
 * Class WeiXinPubOpenid
 * @package app\modules\weiXinPub\models
 *
 * @property integer $id
 * @property string $weixinpub_id
 * @property string $open_id
 * @property integer $user_id
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
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
        return [
            [['weixinpub_id', 'open_id', 'user_id', 'date_created'], 'required'],
            [['user_id'], 'number', 'integerOnly' => true],
            [['weixinpub_id'], 'string', 'max' => 20],
            [['open_id'], 'max' => 40],
            [['date_updated', 'date_deleted'], 'safe']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'weixinpub_id' => 'Weixinpub',
            'open_id' => 'Open',
            'user_id' => 'User',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }


    public static function createModel($wxPubId, $openId, $userId) {
        $model = new self();
        $model->weixinpub_id = $wxPubId;
        $model->open_id = $openId;
        $model->user_id = $userId;
        return $model;
    }

    public function getByWeiXinPubIdAndUserId($wxPubId, $userId) {
        return $this->getByAttributes(array('weixinpub_id' => $wxPubId, 'user_id' => $userId));
    }
    
    public function getByOpenId($openId) {
        return $this->getByAttributes(array('open_id' => $openId));
    }

    public function getWeiXinPubId() {
        return $this->weixinpub_id;
    }

    public function getOpenId() {
        return $this->open_id;
    }

    public function setOpenId($v) {
        $this->open_id = $v;
    }

    public function getUserId() {
        return $this->user_id;
    }

}
