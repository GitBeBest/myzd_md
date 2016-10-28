<?php

/**
 * 微信JSSDK
 *
 * @author zhongtw
 */
namespace app\modules\weiXinPub;

class WechatJSSDK {
    
    
    public function getSignPackage(){
         
        $wechatConfig = new WechatConfig();
        $weixinpub_id = $wechatConfig->getWeixinpubId();
                
        $wechatBaseInfo = new WechatBaseInfo();	
        $wechatAccount = new WechatAccount();
        
        //根据weixinpub_id从数据库获取公众号的app_id
        $result = $wechatAccount->getByPubId($weixinpub_id);
        $app_id = $result['app_id'];
        
        //根据weixinpub_id从数据库获取access_token和jsapi_ticket
	$result = $wechatBaseInfo->getByPubId(Yii::app()->getModule('weixinpub')->weixinpubId);
        $jsapi_ticket = $result['jsapi_ticket'];
        
        $nonceStr = $wechatConfig->createNonceStr(16);
        $timestamp = time();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $app_id,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        
        return $signPackage;
            
    }
    
    
}
