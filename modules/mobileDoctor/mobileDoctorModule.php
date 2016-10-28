<?php

namespace app\modules\mobileDoctor;

use app\assets\AppAsset;
use \yii\base\Module;
use app\modules\weiXinPub\WeiXinPubWebUser;

/**
 * mobileDoctor module definition class
 */
class mobileDoctorModule extends Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\mobileDoctor\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //$this->setViewPath();
        // custom initialization code goes here
        \Yii::$app->setAliases(['mobile-doctor'=> dirname(__FILE__)]);
        \Yii::$app->setComponents([
            'user' => [
                'enableAutoLogin' => true,
                'class' => 'app\modules\weiXinPub\components\WeiXinPubWebUser',
                'identityClass' => 'app\models\user\UserIdentity',
                'loginUrl' => ['/mobileDoctor/doctor/mobile-login']
            ]
        ]);
    }
}
