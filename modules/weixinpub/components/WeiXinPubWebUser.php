<?php

namespace app\modules\weiXinPub\components;
use app\modules\weiXinPub\models\WeiXinPubManager;
use app\util\BaseWebUser;
use app\models\user\UserIdentity;
use yii\web\IdentityInterface;

class WeiXinPubWebUser extends BaseWebUser {

    public function login(IdentityInterface $identity, $duration = 0) {
        parent::login($identity, $duration);
        \Yii::$app->getSession()->set('role', $identity->getRole());

        $wx_pub_manage = new WeiXinPubManager();
        $openid = $wx_pub_manage->getOpenIdFromSession();
        if (isset($openid)) {
            $wx_pub_manage->storeOpenId($openid, $wx_pub_manage->getWeixinpubId(), \Yii::$app->user->id);
        }
    }

}
