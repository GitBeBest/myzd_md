<?php

/**
 * This is the model class for table "contactus".
 *
 * The followings are the available columns in table 'contactus':
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property integer $sent
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\sales;
use app\models\base\BaseActiveRecord;

class Contactus extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'contactus';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sent', 'numerical', 'integerOnly' => true),
            array('name, email, subject', 'length', 'max' => 100),
            array('mobile', 'length', 'max' => 45, 'tooLong' => '{attribute}不可超过45个字'),
            array('message', 'length', 'max' => 500, 'tooLong' => '{attribute}不可超过500个字'),
            array('message, date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, email, subject, message, sent, user_ip, user_agent, access_agent, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'message' => 'Message',
            'sent' => 'Sent',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }
}
