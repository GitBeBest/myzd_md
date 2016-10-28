<?php
namespace app\modules\mobileDoctor\controllers;
use app\controllers\WebsiteController;
use app\util\CaptchaManage;
use yii\helpers\Url;
use yii\captcha\CaptchaAction;
class MobileDoctorController extends WebsiteController {

    public $layout = 'layoutSinglePage';
    public $jqPageId;   //must be unique across all pages in jquery mobile.
    public $pageTitle = '名医主刀医生端';

    public function actions() {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'width' => 125,
                'height' => 30,
                'maxLength' => 6,
                'minLength' => 6
            ]
        ];
    }
    public function init() {
        if ($this->isUserAgentWeixin()) {
            $this->initWeixinOpenId();
        }
       // parent::init();
    }

    public function getHomeUrl() {
        return Url::to('home/index');
    }

    public function setPageID($pid) {
        $this->jqPageId = $pid;
    }

    public function getPageID() {
        return $this->jqPageId;
    }

    public function setPageTitle($title, $siteName = false) {
        parent::setPageTitle($title, $siteName);
    }

    public function getPageTitle() {
        return $this->pageTitle;
    }

    public function showBrowserModeMenu() {
        if ($this->id == 'home') {
            if (isset($_GET['bm'])) {
                return $_GET['bm'] == 1;
            } else if (isset($_POST['bm'])) {
                return $_POST['bm'] == 1;
            } else {
                return $this->isAjaxRequest() === false;
            }
        } else {
            return false;
        }
    }

    public function showActionBar() {
        return ($this->isUserAgentApp() === false);
    }

    public function renderActionBar() {
        if ($this->showActionBar()) {
            $this->renderPartial('//layouts/actionbar');
        }
    }

    public function createPageAttributes($returnString = true) {
        $data = array();
        if (isset($_GET['addBackBtn']) && $_GET['addBackBtn'] == 1) {
            $data['data-add-back-btn'] = 'true';
        }
        if (isset($_GET['backBtnText'])) {
            $data['data-back-btn-text'] = $_GET['backBtnText'];
        }
        if ($returnString) {
            $ret = '';
            foreach ($data as $key => $value) {
                $ret.=$key . '=' . $value . ' ';
            }
            return $ret;
        } else {
            return $data;
        }
    }

    public function initWeiXinOpenId() {
        $wxMgr = new WeixXinpubManager();
        // get weixin_openid from session or db.
        $openid = $wxMgr->getStoredOpenId();
        if (is_null($openid)) {
            // get weixin_openid from new request.            
            //$requestUrl = $this->createUrl('/weixinpub/oauth/getWxOpenIdTest'); //@test
            $requestUrl = $this->createUrl('/weixinpub/oauth/getWxOpenId');
            $currentUrl = $this->getCurrentRequestUrl();
            // store currentUrl in session first, for later call back.
            Yii::app()->session['wx.returnurl'] = $currentUrl;
            $this->redirect($requestUrl);
            Yii::app()->end();
        } else {
            // WeixinpubLog::log("openid: ".$openid, 'info', __METHOD__);
        }
    }

    public function actionValidCaptcha() {
        $output = array('status' => 'no');
        if (strcmp($_REQUEST['co_code'], Yii::app()->session['code']) != 0) {
            $output['status'] = 'no';
            $output['error'] = '图形验证码错误';
        } else {
            $output['status'] = 'ok';
        }
        $this->renderJsonOutput($output);
    }

}
