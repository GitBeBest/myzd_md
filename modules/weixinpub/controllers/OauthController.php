<?php

class OauthController extends WeixinpubController {
    
    public $weixinPubId;
    
    public $weixinAppId;
    
    public $weixinAppSecret;
    
    public $session_key_openid = 'wx.openid';
    
    public function init() {
        parent::init();
        $this->loadWechatAccount();
        $wechatAccount = $this->wechatAccount;
        $this->weixinPubId = $wechatAccount->getWeixinpubId();
        $this->weixinAppId = $wechatAccount->getAppId();  
        $this->weixinAppSecret = $wechatAccount->getAppSecret();     
    }   
    
    public function actionTest() {
        $redirectUrl = $this->createAbsoluteUrl("oauth/getWxOpenId");
        echo $redirectUrl;
    }
    
    /**
     * 在微信浏览器内对已注册过的用户实现自动登录
     * 没有注册的用户返回注册/登录页面
     * @param type $returnUrl
     */
    public function actionAutoLogin($returnUrl,$code = null) {
        if ($code == null) {
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            $redirect_uri = urlencode($this->createAbsoluteUrl("oauth/autoLogin?returnUrl=" . $returnUrl));
            $url = sprintf($url, $this->weixinAppId, $redirect_uri);  
            header('Location: ' . $url);
            Yii::app()->end();
        }        
        $openid = $this->getOpenidFromMp($code);
        if (isset($openid)) {           
            $model = WeixinpubOpenid::model()->getByopenId($openid);
            if(isset($model)){//用户之前已经注册过
                $userid = $model->getUserId();
                $url = Yii::app()->request->hostInfo . "/mobiledoctor/doctor/Wxlogin?userid=" . $userid . "&returnUrl=" . $returnUrl;
                header('Location: ' . $url);
                Yii::app()->end();
            }
        }
        $url = Yii::app()->request->hostInfo . "/mobiledoctor/doctor/mobileLogin?loginType=sms&registerFlag=1";
        header('Location: ' . $url);
        Yii::app()->end();
    }

    public function actionGetWxOpenId() {
        $code = '';
        if (isset($_GET['code'])) {//请求中有code，通过code获取openid
            //$logMsg = '请求中的code为：' . $code . '，开始通过code换取openid';
            //WeixinpubLog::log("$logMsg", 'info', __METHOD__);
            $code = $_GET['code'];
        }else{
           // WeixinpubLog::log('请求中不含code，通过页面跳转获取code', 'info', __METHOD__);
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            $redirect_uri = urlencode($this->createAbsoluteUrl("oauth/getWxOpenId"));
            $url = sprintf($url, $this->weixinAppId, $redirect_uri);  
            header('Location: ' . $url);
            Yii::app()->end();
        }        
        $openid = $this->getOpenidFromMp($code);
        if (isset($openid)) {
            $userId = Yii::app()->user->id;
            $this->saveOpenId($openid, $this->weixinPubId, $userId);
        }
        $returnUrl = Yii::app()->session['wx.returnurl'];
        if (isset($returnUrl)) {
            unset(Yii::app()->session['wx.returnurl']);
            $this->redirect($returnUrl);
            Yii::app()->end();
        }
        $output = new stdClass();
        $output->status = 'ok';
        $output->openid = $openid;
        $this->renderJsonOutput($output);
    }
        
    /**
     * 
     * 通过code从工作平台获取openid及其access_token
     * @param string $code 微信跳转回来带上的code
     * 
     * @return openid
     */
    public function GetOpenidFromMp($code){
        $url = $this->__CreateOauthUrlForOpenid($code);
        //WeixinpubLog::log("通过code换取openid的url为：$url", 'info', __METHOD__);
        //初始化curl
        $ch = curl_init();
        //设置超时
        //curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res,true);
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * 
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     * 
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code){
        $urlObj["appid"] = $this->weixinAppId;
        $urlObj["secret"] = $this->weixinAppSecret;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = CommonConfig::ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }   
    
    /**
     * 将openid与userid关联存入数据库中
     * @param type $openId
     * @param type $wxPubId
     * @param type $userId
     * @return boolean
     */
    private function saveOpenId($openId, $wxPubId, $userId = null) {
        Yii::app()->session[$this->session_key_openid] = $openId;
        if (isset($userId)) {
            $model = WeixinpubOpenid::model()->getByWeixinPubIdAndUserId($wxPubId, $userId);
            if (isset($model) === false) {
                $model = WeixinpubOpenid::createModel($wxPubId, $openId, $userId);
                return $model->save();
            } elseif ($model->open_id != $openId) {
                $model->setOpenId($openId);
                return $model->save(true, array('openId', 'date_updated'));
            }
        }
        return true;
    }
    
}
