<?php


/**
 * 接受来自微信服务器的消息，并处理回复相对应的消息
 *
 * @author zhongtw
 */
namespace app\modules\weiXinPub;

class WechatMessage {

    //接收事件消息
    public function receiveEvent($object) {
        $content = "";
        switch ($object->Event) {
            //用户关注公众号
            case "subscribe":
                $content = "您好，手术直通车是名医主刀为医生打造的一款微信公众号。
                    \n医生可将自己的患者病历上传到微信公众平台，医疗客服为其对接国内相关科室专家，共同为患者确定其最佳合适的诊疗或者转诊方案。
                    \n<a href='http://md.mingyizhudao.com/mobiledoctor/doctor/uploadCert'>注册后请尽快点击完成医生实名认证</a>
                    \n做手术就找名医主刀。";
                break;
            //在模版消息发送任务完成后，微信服务器会将是否送达成功作为通知推送过来
            case "TEMPLATESENDJOBFINISH":
                $wechatMsgRecord = new WechatMsgRecord();
                $wechatMsgRecord->templateSendBack($object->MsgID, $object->Status);
                break;
            default:
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
    
    //接收文本消息，直接转客服处理
    public function receiveText($object) {
        $xmlTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                   </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }    
    
    //回复文本消息
    public function transmitText($object, $content) {
        $xmlTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[text]]></MsgType>
                   <Content><![CDATA[%s]]></Content>
                   </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }  
    
}
