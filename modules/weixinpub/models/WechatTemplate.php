<?php


/**
 * 微信模板消息父类，子类需要根据具体的模板消息来定制属性
 *
 * @author zhongtw
 */
class WechatTemplate {
    
    
    //用户的openid
    private $touser;
    
    //模板ID
    private $template_id;
    
    //跳转的URL
    private $url;
    
    //标题颜色
    private $topcolor;

    
    /**
     * 发送模板消息并返回腾讯处理的结果
     * @param type $data：拼接的消息内容
     * @return type：腾讯返回的json数据
     */
    public function send_TemplateMessage($data) {
        $wechatConfig = new WechatConfig();
        $access_token = $wechatConfig->getAccessToken();
        
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s";       
	$url = sprintf($url, $access_token);
	
	$result = CommonConfig::https_post($url, $data);
        
        //发送记录存入数据库
        $wechatMsgRecord = new WechatMsgRecord();
        $returnJson = json_decode($result, true);
        $dataJson = json_decode($data, true);       
        if($returnJson['errcode'] == 0){
            $wechatMsgRecord->msgid = $returnJson['msgid'];
        }        
        $wechatMsgRecord->from_username = $wechatConfig->getWeixinpubId();
        $wechatMsgRecord->to_username = $dataJson['touser'];
        $wechatMsgRecord->send_content = $data;
        $wechatMsgRecord->send_status = $returnJson['errcode'];
        $wechatMsgRecord->save();
        
        return $result;
    }
    
}
