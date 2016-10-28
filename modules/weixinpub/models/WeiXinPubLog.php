<?php

/**
 * This is the model class for table "weixinpub_log".
 *
 * The followings are the available columns in table 'weixinpub_log':
 * @property integer $id
 * @property string $level
 * @property string $category
 * @property string $message
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\modules\weiXinPub;
use app\models\base\BaseActiveRecord;

class WeiXinPubLog extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'weixinpub_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('level', 'length', 'max' => 10),
            array('category', 'length', 'max' => 128),
            array('message, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, level, category, message, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'message' => 'Message',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * @param $message
     * @param string $level
     * @param string $category ('info', 'warning', 'error')
     * @return bool
     */
    public static function log($message, $level = 'info', $category = 'application') {
        $model = new self();
        $model->message = $message;
        $model->level = $level;
        $model->category = $category;
        return $model->save();
    }

}
