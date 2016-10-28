<?php

/**
 * 一些通用的方法
 *
 * @author zhongtw
 */
namespace app\modules\weiXinPub;
use yii\log\Logger;
use app\modules\weiXinPub\WeiXinPubLog;

class CommonConfig {


    /**
     * 将array转为xml，输出xml字符
     * @param $arr
     * @return string
     */
    public static function ToXml($arr){
        if(!is_array($arr) || count($arr) <= 0){
            \Yii::getLogger()->log("数组数据异常", Logger::LEVEL_ERROR);
    	}    	
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml; 
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    public static function FromXml($xml){	
        if(!$xml){
            $message = "转化的内容不是xml格式";
            WeixinpubLog::log($message, error, __METHOD__);
        }       
        libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
        return $arr;
    }

    /**
     * 格式化参数,格式化成url参数
     * @param $arr
     * @return string
     */
    public static function ToUrlParams($arr){
        $buff = "";
        foreach ($arr as $k => $v){
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取随机字符串，可自定义字符串长度
     * @param $length
     * @return string
     */
    public static function createNonceStr($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }    
    
    /** 
     * 发送post请求 
     * @param string $url 请求地址 
     * @return string 
     */   
    public static function https_post($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if(!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    
    
}
