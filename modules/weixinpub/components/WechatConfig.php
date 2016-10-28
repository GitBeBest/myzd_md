<?php
/**
 * @author zhongtw
 */
namespace app\modules\weiXinPub;
use app\modules\weiXinPub\WeChatBaseInfo;
use yii\base\Exception;

class WeChatConfig {

    //根据appid和secret获取accesstoken的请求接口
    const URL_ACCESS_TOKEN  = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
    
    //根据access_token获取jsapi_ticket的请求接口
    const URL_JS_API_TICKET  = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi";
     
    //获取创建二维码ticket
    const  URL_QR_CODE = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
    
    const  URL_SHOW_QR_CODE = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s";
  
    public function getUrlQrcode() {
        return self::URL_QR_CODE;
    }
        
    public function getUrlShowQrcode() {
        return self::URL_SHOW_QR_CODE;
    }
    
    //从模块配置中获取二维码存取路径
    public function getQrcodetPath() {
        return \Yii::$app->getModule('weixinpub')->qr_code_path;
    }
    
    //从模块配置文件中获取微信ID
    public function getWeixinpubId() {
        return \Yii::$app->getModule('weixinpub')->weixinpubId;
    }

    /**
     * 1、根据 app_id 和 app_secret 获取 access_token
     * 2、根据 wei_xin_pub_id 将 access_token 存入数据库
     * @param $wei_xin_pub_id
     * @param $app_id
     * @param $app_secret
     */
    public function fetchAccessToken($wei_xin_pub_id, $app_id, $app_secret) {
        //通过微信接口获取access_token
        $url = sprintf(self::URL_ACCESS_TOKEN, $app_id, $app_secret);
        $returnStr = file_get_contents($url);
        $returnJson = json_decode($returnStr, true);
        if(isset($returnJson["errcode"]) && $returnJson["errcode"] != 0){
            echo $returnStr;
           \Yii::$app->end();
        }
        $access_token = $returnJson['access_token'];              
        //根据weixinpub_id将access_token存入数据库      
        $weChatBaseInfo = new WechatBaseInfo();
        if($weChatBaseInfo->isExists($wei_xin_pub_id)){
            $weChatBaseInfo->updateAccessTokenByPubId($wei_xin_pub_id, $access_token);
        }              
    }

    /**
     * 根据access_token获取jsapi_ticket并根据weixinpub_id将其存入数据库
     * @param $wei_xin_pub_id
     */
    public function fetchJsApiTicket($wei_xin_pub_id) {
        $weChatBaseInfo = new WechatBaseInfo();
        //根据weixinpub_id从数据库中获取相对应的access_token
        $access_token = $weChatBaseInfo->getByPubId($wei_xin_pub_id)->getAccessToken();
        //根据access_token请求微信接口获取jsapi_ticket
        $url = sprintf(self::URL_JS_API_TICKET, $access_token);
        $returnStr = file_get_contents($url);
        $returnJson = json_decode($returnStr, true);
        //微信返回errcode不为空并且不等于0，则打印错误信息
        if(isset($returnJson["errcode"]) && $returnJson["errcode"] != 0){
            echo $returnStr;
            Yii::app()->end();
        }
        $js_api_ticket = $returnJson['ticket'];
        //根据weixinpub_id将jsapi_ticket存入数据库
        $weChatBaseInfo->updateJsapiTicketByPubId($wei_xin_pub_id, $js_api_ticket);
    }
       
    /**
     * 根据 WEIXINPUP_ID 获取相对应的access_token
     * @return type
     */
    public function getAccessToken(){
        $weChatBaseInfo = new WechatBaseInfo();
	    $result = $weChatBaseInfo->getByPubId($this->getWeixinpubId());
	    $access_token = $result['access_token'];
        return $access_token;
    }

    /**
     * 通过$url从微信服务器下载图片
     * @param $url
     * @return array
     */
    function downloadImageFromWeiXin($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $http_info = curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array('body' => $package), array('header' => $http_info));
    }

    /**
     * 根据url生成带logo的二维码
     * @param string $url 二维码内容
     * @param string $scene_str 二维码名称
     * @return string
     */
    function getQRcodeByUrl($url, $scene_str){
        try{
            require_once '/protected/modules/weixinpub/phpqrcode/phpqrcode.php';
            $value = $url; //二维码内容 
            $name = $scene_str;
            $errorCorrectionLevel = 'L';//容错级别 
            $matrixPointSize = 8;//生成图片大小 
            //生成二维码图片 
            $qrcodetPath = $this->getQrcodetPath();
            QRcode::png($value, "$qrcodetPath\\$name.png", $errorCorrectionLevel, $matrixPointSize, 2); 
            $logo = "$qrcodetPath\myzd.png";//准备好的logo图片 
            $QR = "$qrcodetPath\\$name.png";//已经生成的原始二维码图 

            if ($logo !== FALSE) { 
                $QR = imagecreatefromstring(file_get_contents($QR)); 
                $logo = imagecreatefromstring(file_get_contents($logo)); 
                $QR_width = imagesx($QR);//二维码图片宽度 
                $QR_height = imagesy($QR);//二维码图片高度 
                $logo_width = imagesx($logo);//logo图片宽度 
                $logo_height = imagesy($logo);//logo图片高度 
                $logo_qr_width = $QR_width / 5; 
                $scale = $logo_width/$logo_qr_width; 
                $logo_qr_height = $logo_height/$scale; 
                $from_width = ($QR_width - $logo_qr_width) / 2; 
                //重新组合图片并调整大小 
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
                $logo_qr_height, $logo_width, $logo_height);        
            } 
            //输出图片 
            imagepng($QR, "$qrcodetPath\\$name.png");
            $arr = array ("flag"=>1, "info"=>"SUCCESS");
        }catch(Exception $e){
            $arr = array ("flag"=>0, "info"=>"FAIL");
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE); 
    }

    /**
     * 微信支付签名算法
     * @param $arr
     * @return string
     */
    public function MakeSign($arr){    
        ksort($arr);//签名步骤一：按字典序排序参数        
        $string = CommonConfig::ToUrlParams($arr);//格式化参数       
        $string = $string . "&key=".WxPayConfig::KEY;//签名步骤二：在string后加入KEY        
        $string = md5($string);//签名步骤三：MD5加密        
        $result = strtoupper($string);//签名步骤四：所有字符转为大写
        return $result;
    }

    
}
