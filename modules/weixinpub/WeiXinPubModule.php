<?php
namespace app\modules\weiXinPub;

use yii\base\Module;

class WeiXinPubModule extends Module {
    
    //公众号ID
    public $wei_xin_pub_id = 'myzdztc';
    //public $weixinpubId = 'myzdtest';
    
    //微信二维码存取路径
    public $qr_code_path = 'qrcode';

    public function beforeAction($controller, $action) {
        if (parent::beforeAction($action)) {
            return true;
        } else
            return false;
    }

    public function getPubId()
    {
        return $this->wei_xin_pub_id;
    }

    public function getCodePath()
    {
        return $this->qr_code_path;
    }
}
