<?php
namespace app\controllers;

use app\models\core\CoreRasConfig;
use app\models\user\User;
use app\util\RsaEncrypt;
use yii\base\Exception;
use yii\console\Response;
use yii\helpers\Json;
use yii\web\Controller;

require_once('../util/helper.php');

abstract class BaseController extends Controller {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout;
    public $defaultAction;
    public $returnUrl = '';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu;
    public $pageTitle;

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $current_user = null;

    public $enableCsrfValidation = false;

    /**
     * 获取当前登录用户
     * @return null|User
     */
    public function getCurrentUser() {
        try {
            return $this->loadUser();
        } catch(Exception $e) {
            return null;
        }
    }

    /**
     * @return int|string
     */
    public function getCurrentUserId() {
        return \Yii::$app->user->id;
    }

    /**
     * @return null|User
     */
    private function loadUser() {
        if(is_null($this->current_user)) {
            if(isset(\Yii::$app->user->id)) {
                $this->current_user = User::findOne(['id' => \Yii::$app->user->id]);
                if(is_null($this->current_user)) {
                    \Yii::$app->user->logout();
                }
            }
        }
        return $this->current_user;
    }

    /**
     * 获取返回url
     * @param string $default_url
     * @return string
     */
    public function getReturnUrl($default_url = '') {
        if(strIsEmpty($this->returnUrl)) {
            $this->returnUrl = trim(\Yii::$app->request->get('returnUrl', $default_url));
        }
        return $this->returnUrl;
    }

    public function isUserAgentApp() {
        return ((isset($_GET['appv']) && $_GET['appv'] > 1) ||
            (isset($_GET['agent']) && $_GET['agent'] == 'app') ||
            (isset($_POST['agent']) && $_POST['agent'] == 'app') ||
            (isset($_GET['os']) && in_array($_GET['os'], array('android', 'ios'))) ||
            (isset($_POST['os']) && in_array($_POST['os'], array('android', 'ios')))
        );
    }

    public function isUserAgentWeiXin() {
        $userAgent = \Yii::$app->request->getUserAgent();
        return (strContains($userAgent, 'MicroMessenger'));
    }

    public function isUserAgentIOS() {
        $userAgent = strtolower(\Yii::$app->request->getUserAgent());
        return strContains($userAgent, 'iphone') || strContains($userAgent, 'ipad');
    }

    public function isUserAgentAndroid() {
        $userAgent = strtolower(\Yii::$app->request->getUserAgent());
        return strContains($userAgent, 'android');
    }

    public function headerUTF8() {
        \Yii::$app->getResponse()->getHeaders()->set('Content-Type: text/html; charset=utf-8');
    }
    /**
     * 请求参数解密
     * @param bool|true $dis_for
     * @return mixed|string
     */
    public function decryptInput($dis_for = true) {
        $param = $_POST['param'];
        $inputs = Json::decode($param, true);
        $rsa_config = new CoreRasConfig();
        $rasConfig = $rsa_config->getByClient('app');
        $encrypt = new RsaEncrypt($rasConfig->public_key, $rasConfig->private_key);
        $str = $encrypt->newDecrypt($inputs);
        $str = base64_decode($str);
        $str = Json::decode($str, true);
        if ($dis_for) {
            foreach ($str as $k => $values) {
                foreach ($values as $key => $value) {
                    $values[$key] = urldecode($value);
                }
                $str[$k] = $values;
            }
        }
        return $str;
    }

    public function renderJsonOutput($data, $exit = true, $httpStatus = 200) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }
}
