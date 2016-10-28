<?php


/**
 * 微信定时任务
 * 遍历wechat_account表中的所有weixinpub_id定时刷新access_token和jsapi_ticket
 * 刷新成功返回：{"errcode":0,"errmsg":"SUCCESS"}
 * 刷新失败返回：{"errcode":非0,"errmsg":"其它"}
 * 
 * @author zhongtw
 */
class WechattaskController extends WeixinpubController {
    
    
    public $allWechatAccount;
    
    public $wechatConfig;
   
    public function init(){           
        parent::init();    
        $this->allWechatAccount = WechatAccount::model()->getAll();     
        $this->wechatConfig = new WechatConfig();        
    }
    
    //需要定时刷新
    public function actionFetch() {
        $output = new stdClass();
        try{ 
            foreach ($this->allWechatAccount as $v){
                $weixinpub_id = $v['weixinpub_id'];
                $app_id = $v['app_id'];
                $app_secret = $v['app_secret'];
                $this->wechatConfig->fetchAccessToken($weixinpub_id, $app_id, $app_secret);
                $this->wechatConfig->fetchJsApiTicket($weixinpub_id);
            } 
            $output->errcode = 0;
            $output->errmsg = 'SUCCESS';
        }catch(Exception $e){
            WeixinpubLog::log($e, error, __METHOD__);
            $output->errcode = 1;
            $output->errmsg = $e;
        }
        echo $this->renderJsonOutput($output);
        Yii::app()->end();
    }
   
    
}
