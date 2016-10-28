<?php

/**
 * 生成带参数二维码（暂未正式使用）
 *
 * @author Administrator
 */
class QrcodeController extends WeixinpubController {
    
    
    //根据参数生成永久二维码
    public function actionForever(){     
        
        
        //判断参数是否为空
        if (!isset($_GET["scene_str"])){
            echo 'missing parameters:scene_str';
            exit;
        }
        
        try{
            //获取参数
            $scene_str = $_GET["scene_str"];
            
            //拼接请求json数据
            $arr = array ("action_name"=>"QR_LIMIT_STR_SCENE", "action_info"=>array("scene"=>array("scene_str"=>$scene_str)));
            $json_string = json_encode($arr,JSON_UNESCAPED_UNICODE);
            
            //获取请求URL
            $wechatConfig = new WechatConfig();
            $url = $wechatConfig->getUrlQrcode();
            
            $wechatBaseInfo = new WechatBaseInfo();
            $weixinpub_id = $wechatConfig->getWeixinpubId();
            
            $result = $wechatBaseInfo->getByPubId($weixinpub_id);
            $access_token = $result['access_token'];   
            
            $url = sprintf($url, $access_token);

            //通过http请求获取数据
            $result = $wechatConfig->https_post($url, $json_string);
            $jsoninfo = json_decode($result, true);
            //微信接口返回错误，返回错误信息
            if(isset($jsoninfo["errcode"]) && $jsoninfo["errcode"] != 0){
                echo $result;
                exit;
            }
            
//生成二维码图片有两种方式，一是通过ticket从微信下载二维码，二是获取url后自己生成二维码，这样可以自己加LOGO
            
//            1、根据微信返回的ticket从微信服务器下载二维码
//            $ticket = urlencode($jsoninfo["ticket"]);
//            $url = sprintf($wechatConfig->getUrlShowQrcode(), $ticket);
//            $imageInfo = $wechatConfig->downloadImageFromWeiXin($url);
//            $filename = "$scene_str.jpg";
//            $local_file = fopen($filename, 'w');
//            if(false !== $local_file){
//                if(false !== fwrite($local_file, $imageInfo["body"])){
//                    fclose($local_file);
//                }
//            }
           
//          2、根据微信返回的url生成二维码
            $qrcodeUrl = $jsoninfo["url"];  
            $result = $wechatConfig ->getQRcodeByUrl($qrcodeUrl, $scene_str);
                       
            $arr = array ("flag"=>1, "info"=>"SUCCESS");
        }catch(Exception $e){
            $arr = array ("flag"=>0, "info"=>"FAIL");
        }
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        exit;     
    }
    

    /**
     * 生成临时二维码
     * @param type $param
     */
    function actionTemporary($url) {
        require_once 'protected/modules/weixinpub/phpqrcode/phpqrcode.php';
        QRcode::png($url);
    }
    
    
}
