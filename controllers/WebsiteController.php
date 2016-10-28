<?php
namespace app\controllers;

use \app\controllers\BaseController;
use app\models\base\CoreAccess;
use app\models\MongoDbManager;

/**
 * Class WebsiteController
 * @package app\controllers
 */
abstract class WebsiteController extends BaseController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/layoutMain';
    public $defaultAction = 'index';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    public $pageTitle = '名医主刀_三甲医院名医,专家,主任医生手术,床位预约,网上挂号,手机APP';
    public $htmlMetaKeywords = '名医主刀,三甲医院,名医,专家,主任医生,手术预约,网上挂号,手机APP';
    public $htmlMetaDescription = '名医随时有,手术不再难！【名医主刀】汇聚国内外顶级名医资源和床位资源，利用互联网技术实现医患精准匹配，帮助广大患者得以在第一时间预约到名医专家进行主刀治疗。www.mingyizhudao.com';

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $content_container = 'container page-container';
    public $site_menu = null;
    public $show_header = true;
    public $show_header_navbar = true;
    public $show_footer = true;
    public $show_traffic_script = true;
    public $show_baiDuShangQiao = true;

    public function init() {
        if (isset(\Yii::$app->view->theme)) {
            \Yii::$app->view->jsFiles = array(
                'jquery.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery-1.9.1.min.js',
                'jquery.min.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery-1.9.1.min.js',
                'jquery.yiiactiveform.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery.yiiactiveform.js',
            );
        }

        \Yii::$app->view->registerJsFile('jquery');

        // show header.
        if (isset($_GET['header']) && $_GET['header'] != 1) {
            $this->show_header = false;
        }
        // show footer.
        if (isset($_GET['footer']) && $_GET['footer'] != 1) {
            $this->show_footer = false;
        }
        $this->storeUserAccessInfo();
        return parent::init();
    }

    /**
     * Stores user's access info for every request.
     */
    public function storeUserAccessInfo()
    {
        $time = time();
        if (get_extension_funcs("mongodb") !== false) {
            $mongodb = new MongoDbManager();
            $mongodb->insert([
                'source' => 'doctor',
                'user_host_ip' => \Yii::$app->request->userIP,
                'url' => \Yii::$app->request->url,
                'url_referrer' => \Yii::$app->request->referrer,
                'user_agent' => \Yii::$app->request->userAgent,
                'user_host' => \Yii::$app->request->userHost,
                'timestamp' => $time,
                'date_time' => date('Y-m-d H:i:s', $time)
            ]);
        }
        elseif (get_extension_funcs("mongo") !== false) {
//            $mongo = new MongoManager();
//            $mongo->source = 'doctor';
//            $mongo->user_host_ip = Yii::app()->request->getUserHostAddress();
//            $mongo->url = Yii::app()->request->getUrl();
//            $mongo->url_referrer = Yii::app()->request->getUrlReferrer();
//            $mongo->user_agent = Yii::app()->request->getUserAgent();
//            $mongo->user_host = Yii::app()->request->getUserHost();
//            $mongo->timestamp = $time;
//            $mongo->date_time = date('Y-m-d H:i:s', $time);
//            $mongo->addInfo();
        }
        else {
            $coreAccess = new CoreAccess();
            $coreAccess->user_host_ip = \Yii::$app->request->userIP;
            $coreAccess->url = \Yii::$app->request->url;
            $coreAccess->url_referrer = \Yii::$app->request->referrer;
            $coreAccess->user_agent = \Yii::$app->request->userAgent;
            $coreAccess->user_host = \Yii::$app->request->userHost;
            $coreAccess->save();
        }
    }
}
