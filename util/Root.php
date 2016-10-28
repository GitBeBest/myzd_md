<?php

/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/10
 * Time: 17:24
 */
namespace app\util;
use app\models\user\User;
use yii\helpers\Json;
use yii\helpers\Url;

class Root
{
    public static $currentUser;

    /**
     * @param null $with
     * @return User
     */
    public static function loadUser($with= null) {
        if (is_null(self::$currentUser)) {
            if (isset(\Yii::$app->user->id)) {
                self::$currentUser = User::findOne(\Yii::$app->user->id);
                if (is_null(self::$currentUser)) {
                    // 有session但存的user.id不存在，所以logout user.
                    \Yii::$app->user->logout();
                }
            }
        }
        return self::$currentUser;
    }

    public static function getCurrentRequestUrl() {
        $request = \Yii::$app->request;
        return $request->hostInfo . $request->url;
    }

    public static function redirect($url)
    {
        \Yii::$app->getResponse()->redirect(\Yii::$app->user->loginUrl);
    }

    public static function renderJsonOutput($data, $exit = true, $httpStatus = 200) {
        header('Content-Type: application/json; charset=utf-8');
        echo Json::encode($data);
//        foreach (\Yii::$app->log->routes as $route) {
//            if ($route instanceof CWebLogRoute || $route instanceof XWebDebugRouter) {
//                $route->enabled = false; // disable any weblogroutes
//            }
//        }
        if ($exit) {
            \Yii::$app->end($httpStatus, true);
        }
    }
}