<?php
namespace app\modules\weiXinPub\models;

class WeiXinPubManager {
    
    public $session_key_openid = 'wx.openid';

    public function getStoredOpenId() {
        if (isset(\Yii::$app->session[$this->session_key_openid])) {
            // get openid from session.
            return \Yii::$app->session[$this->session_key_openid];
        } elseif (isset(\Yii::$app->user->id)) {
            // get openid from db.
            $userId = \Yii::$app->user->id;
            $wx_pub_id = \Yii::$app->getModule('weixinpub')->weixinpubId;
            $model = (new WeiXinPubOpenid())->getByWeixinPubIdAndUserId($wx_pub_id, $userId);
            if (isset($model)) {
                $openId = $model->getOpenId();  // store openid in session.
                \Yii::$app->getSession()->set($this->session_key_openid, $openId);
                return $openId;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function getOpenIdFromSession(){
        return \Yii::$app->getSession()->get($this->session_key_openid);
    }

    public function storeOpenId($openId, $wxPubId, $userId = null) {
        \Yii::$app->getSession()->set($this->session_key_openid, $openId);
        if (isset($userId)) {
            $wx_pub_id = \Yii::$app->getModule('weiXinPub')->getPubId();

            $model = (new WeiXinPubOpenid())->getByWeixinPubIdAndUserId($wx_pub_id, $userId);
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
    
    public function getWeiXinPubId(){
        return \Yii::$app->getModule('weiXinPub')->getPubId();
    }
    
    

}
