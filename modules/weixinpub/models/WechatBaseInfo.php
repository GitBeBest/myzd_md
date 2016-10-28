<?php

/**
 * This is the model class for table "wechat_base_info".
 *
 * The followings are the available columns in table 'wechat_base_info':
 * @property integer $id
 * @property string $weixinpub_id
 * @property string $access_token
 * @property string $jsapi_ticket
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */

namespace app\modules\weiXinPub;
use app\models\base\BaseActiveRecord;
date_default_timezone_set('PRC'); 
define("CURRENT_TIME", date("Y-m-d H:i:s"));

class WeChatBaseInfo extends BaseActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'wechat_base_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weixinpub_id', 'required'),
			array('weixinpub_id', 'length', 'max'=>20),
			array('access_token, jsapi_ticket', 'length', 'max'=>512),
			array('date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, weixinpub_id, access_token, jsapi_ticket, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'weixinpub_id' => '微信账号',
			'access_token' => '公众号的全局唯一票据',
			'jsapi_ticket' => '调用微信JS接口的临时票据',
			'date_created' => '创建时间',
			'date_updated' => '最后修改时间',
			'date_deleted' => '删除时间',
		);
	}

    /***********************************************以下为自定义方法***********************************************/


	/**
	 * 根据weixinpub_id查看基本信息是否存在，不存在就插入一条数据
	 * @param $wei_xin_pub_id
	 * @return bool
	 */
    public function isExists($wei_xin_pub_id){
        $exists = WechatBaseInfo::model()->exists("weixinpub_id=:weixinpub_id",array(":weixinpub_id"=>$wei_xin_pub_id));
        if($exists){
            return TRUE;
        }     
        $this->weixinpub_id = $wei_xin_pub_id;
        if($this->save() > 0){ 
            return TRUE;
        }else{ 
            return FALSE;           
        }
    }

    public function getByPubId($wei_xin_pub_id){
        return $this->getByAttributes(array('weixinpub_id'=>$wei_xin_pub_id));
    }

    /**
     * 根据 weixinpub_id 修改 access_token
     * @param type $wei_xin_pub_id
     * @param type $access_token
     * @return type
     */
    public function updateAccessTokenByPubId($wei_xin_pub_id, $access_token){
        $count = WechatBaseInfo::model()->updateAll(
                array('access_token'=>$access_token, 'date_updated'=>CURRENT_TIME),
                'weixinpub_id=:weixinpub_id',
                array(':weixinpub_id'=>$wei_xin_pub_id)
            );
        return $count;
    }
    
    
    /**
     * 根据 weixinpub_id 修改 jsapi_ticket
     * @param type $wei_xin_pub_id
     * @param type $js_api_ticket
     * @return type
     */
    public function updateJsapiTicketByPubId($wei_xin_pub_id, $js_api_ticket){
        $count = WechatBaseInfo::model()->updateAll(
                array('jsapi_ticket'=>$js_api_ticket, 'date_updated'=>CURRENT_TIME),
                'weixinpub_id=:weixinpub_id',
                array(':weixinpub_id'=>$wei_xin_pub_id)
            );
        return $count;
    }
    
    public function getAccessToken(){
        return $this->access_token;
    }
    
    public function getJsapiTicket(){
        return $this->jsapi_ticket;
    }
    
    
}
