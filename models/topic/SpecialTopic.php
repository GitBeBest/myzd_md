<?php

/**
 * This is the model class for table "special_topic".
 *
 * The followings are the available columns in table 'special_topic':
 * @property integer $id
 * @property string $topic
 * @property string $content_url
 * @property string $banner_url
 * @property integer $like_count
 * @property string $date_published
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property SpecialTopicUserLike[] $specialTopicUserLikes
 */
namespace app\models\topics;
use app\models\base\BaseActiveRecord;

class SpecialTopic extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'special_topic';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('topic, date_created', 'required'),
            array('like_count, display_order', 'numerical', 'integerOnly' => true),
            array('topic', 'length', 'max' => 200),
            array('content_url, banner_url', 'length', 'max' => 1000),
            array('date_published, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, topic, content_url, banner_url, like_count, date_published, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'specialTopicUserLikes' => array(self::HAS_MANY, 'SpecialTopicUserLike', 'special_topic_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => '专题id',
            'topic' => '专题名',
            'content_url' => '专题页面链接',
            'banner_url' => '专题图片链接',
            'like_count' => 'Like Count',
            'date_published' => '发布日期',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }
}
