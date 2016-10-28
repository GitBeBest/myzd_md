<?php


/**
 * 医生端模板消息
 * 公众号：手术直通车
 * @author zhongtw
 */
class SendtempmessageController extends WeixinpubController {
    
    /**
     * 订单支付成功
     */
    public function actionPaysuccess(){
        
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postStr = json_decode($postStr, true);
        
        $template = new TemplatePaymentSuccess();              
        $template->touser = $postStr["touser"];
        $template->url = $postStr["url"];
        $template->first_Value = $postStr["first_Value"];
        $template->keyword1_Value = $postStr["keyword1_Value"];
        $template->keyword2_Value = $postStr["keyword2_Value"];
        $template->keyword3_Value = $postStr["keyword3_Value"];
        $template->remark_Value = $postStr["remark_Value"];
        
        $result = $template->getTemplateMessage($template);
        echo $result;
        Yii::app()->end();
    }
    
    
    /**
     * 订单未支付通知
     */
    public function actionUnpaid(){
        
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postStr = json_decode($postStr, true);
        
        $template = new TemplateUnpaid();
        $template->touser = $postStr["touser"];
        $template->url = $postStr["url"];
        $template->first_Value = $postStr["first_Value"];
        $template->ordertape_Value = $postStr["ordertape_Value"];
        $template->ordeID_Value = $postStr["orderID_Value"];
        $template->remark_Value = $postStr["remark_Value"];
        
        $result = $template->getTemplateMessage($template);
        echo $result;
        Yii::app()->end();
    }
    
    
    /**
     * 订单状态更新
     */
    public function actionUpdatestatus(){
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postStr = json_decode($postStr, true);
        
        $template = new TemplateUpdateStatus();
        $template->touser = $postStr["touser"];
        $template->url = $postStr["url"];
        $template->first_Value = $postStr["first_Value"];
        $template->keyword1_Value = $postStr["keyword1_Value"];
        $template->keyword2_Value = $postStr["keyword2_Value"];
        $template->remark_Value = $postStr["remark_Value"];
        
        $result = $template->getTemplateMessage($template);
        echo $result;
        Yii::app()->end();
    }
    
    
    /**
     * 新订单提醒
     */
    public function actionOrdernotice(){
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postStr = json_decode($postStr, true);
        
        $template = new TemplateOrderNotice();
        $template->touser = $postStr["touser"];
        $template->url = $postStr["url"];
        $template->first_Value = $postStr["first_Value"];
        $template->keyword1_Value = $postStr["keyword1_Value"];
        $template->keyword2_Value = $postStr["keyword2_Value"];
        $template->keyword3_Value = $postStr["keyword3_Value"];
        $template->keyword4_Value = $postStr["keyword4_Value"];
        $template->keyword5_Value = $postStr["keyword5_Value"];
        $template->remark_Value = $postStr["remark_Value"];
        
        $result = $template->getTemplateMessage($template);
        echo $result;
        Yii::app()->end();
    }
    
    
    /**
     * 审核结果通知
     */
    public function actionReviewnotice(){
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $postStr = json_decode($postStr, true);
        
        $template = new TemplateReviewNotice();
        $template->touser = $postStr["touser"];
        $template->url = $postStr["url"];
        $template->first_Value = $postStr["first_Value"];
        $template->keyword1_Value = $postStr["keyword1_Value"];
        $template->keyword2_Value = $postStr["keyword2_Value"];
        $template->keyword3_Value = $postStr["keyword3_Value"];
        $template->remark_Value = $postStr["remark_Value"];
        
        $result = $template->getTemplateMessage($template);
        echo $result;
        Yii::app()->end();
    }
    
}
