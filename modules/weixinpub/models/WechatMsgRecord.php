<?php

/**
 * This is the model class for table "wechat_msg_record".
 *
 * The followings are the available columns in table 'wechat_msg_record':
 * @property integer $id
 * @property integer $msgid
 * @property string $from_username
 * @property string $to_username
 * @property string $send_time
 * @property string $send_content
 * @property string $send_status
 * @property string $back_time
 * @property string $back_status
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class WechatMsgRecord extends EActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wechat_msg_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('msgid', 'numerical', 'integerOnly'=>true),
			array('from_username', 'length', 'max'=>20),
			array('to_username', 'length', 'max'=>50),
			array('send_content', 'length', 'max'=>512),
			array('send_status, back_status', 'length', 'max'=>11),
			array('send_time, back_time, date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, msgid, from_username, to_username, send_time, send_content, send_status, back_time, back_status, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'msgid' => 'Msgid',
			'from_username' => 'From Username',
			'to_username' => 'To Username',
			'send_time' => 'Send Time',
			'send_content' => 'Send Content',
			'send_status' => 'Send Status',
			'back_time' => 'Back Time',
			'back_status' => 'Back Status',
			'date_created' => 'Date Created',
			'date_updated' => 'Date Updated',
			'date_deleted' => 'Date Deleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('msgid',$this->msgid);
		$criteria->compare('from_username',$this->from_username,true);
		$criteria->compare('to_username',$this->to_username,true);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('send_content',$this->send_content,true);
		$criteria->compare('send_status',$this->send_status,true);
		$criteria->compare('back_time',$this->back_time,true);
		$criteria->compare('back_status',$this->back_status,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_updated',$this->date_updated,true);
		$criteria->compare('date_deleted',$this->date_deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WechatMsgRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
    /***********************************************以下为自定义方法***********************************************/
        
        /**
         * 根据消息ID修改微信服务器返回的模板消息送达状态
         * @param type $msgid       消息ID
         * @param type $back_time   返回时间
         * @param type $back_status 返回用户接受模板消息的状态
         */
        public function templateSendBack($msgid, $back_status){
            return WechatMsgRecord::model()->updateAll(array('back_status'=>$back_status), 'msgid=:msgid', array(':msgid'=>$msgid));
        }
        
        
}
