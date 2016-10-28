<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/10
 * Time: 17:18
 */

namespace app\modules\mobileDoctor\filter;


use app\util\Root;
use yii\base\ActionFilter;
use yii\helpers\Url;

class UserContext extends  ActionFilter
{
    public function beforeAction($action)
    {
        $user = Root::loadUser();
        if (is_null($user)) {
            $redirectUrl = Url::to('doctor/mobileLogin');
            $currentUrl = Root::getCurrentRequestUrl();
            $redirectUrl.='?returnUrl=' . $currentUrl;
            Root::redirect($redirectUrl);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

}