<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/27
 * Time: 14:20
 */

namespace app\modules\mobileDoctor\filter;


use app\util\Root;
use yii\base\ActionFilter;
use yii\web\HttpException;

class UserDoctorContext extends ActionFilter
{
    public function beforeAction($action)
    {
        $user = Root::loadUser(array('userDoctorProfile'));
        if ($user->isDoctor(false) === false) {
            throw new HttpException(404, 'The requested page does not exist.');
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}