<?php

/**
 * This is the model class for table "msg_sms_template".
 *
 * The followings are the available columns in table 'msg_sms_template':
 * @property integer $id
 * @property string $code
 * @property string $vendor_name
 * @property string $vendor_template_id
 * @property string $content
 * @property string $module
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\sales;
use app\models\base\BaseActiveRecord;

class MsgSmsTemplate extends BaseActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'msg_sms_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[['code','content'], 'required'],
			[['code, vendor_name, module'], 'length', 'max' => 50],
			[['vendor_template_id'], 'length', 'max'=>10],
			[['content, remark'], 'length', 'max' => 200],
			[['date_created, date_updated, date_deleted'], 'safe'],
			[['id, code, vendor_name, vendor_template_id, content, module, remark, date_created, date_updated, date_deleted'], 'safe', 'on'=>'search']
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => '编码',
			'vendor_name' => '短信供应商的名称',
			'vendor_template_id' => '短信供应商的模板id',
			'content' => '短信内容',
			'module' => '模块名称',
			'remark' => '备注',
			'date_created' => 'Date Created',
			'date_updated' => 'Date Updated',
			'date_deleted' => 'Date Deleted',
		);
	}
}
