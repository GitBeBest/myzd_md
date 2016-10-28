<?php
namespace app\modules\weiXinPub;
use app\controllers\BaseController;

class WeiXinPubController extends BaseController {
    

    public $weixinpubId;
    
    public $wechatAccount;


    public function init() {
        parent::init();
        $this->weixinpubId = \Yii::$app->getModule('weixinpub')->getPubId();
    }

    /**
     * 根据weixinpubId获取单个wechatAccount
     */
    public function loadWechatAccount(){
        if (is_null($this->wechatAccount)){
            $wechatAccount = new WechatAccount();
            $this->wechatAccount = $wechatAccount->getByPubId($this->weixinpubId);
        }
    }
   

}
