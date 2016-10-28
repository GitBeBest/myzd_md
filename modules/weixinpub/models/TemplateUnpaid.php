<?php

/**
 * 订单未支付通知
 *
 * @author Administrator
 */
class TemplateUnpaid extends WechatTemplate {
    
    
    //标题
    private $first_Value;
    
    private $first_Color;
    
    //下单时间
    private $ordertape_Value;
    
    private $ordertape_Color;
    
    //订单号
    private $ordeID_Value;
    
    private $ordeID_Color;
    
    private $remark_Value;
    
    private $remark_Color;
    
    
    public function __set($property_name, $value){
        $this->$property_name = $value;
    }
    
    public function __get($property_name){
        if(isset($this->$property_name)){
            return($this->$property_name);
        }else{
            return(NULL);
        }
    }
    
    
    public function getTemplateMessage($template) {
       
        //每个模板消息的模板ID都是固定的
        $template->template_id = "BpJFg3yl3pZ2TgtZ5pSf_Nr5yedoj1qutu570VQjzNs";
        //$template->template_id = "kYlJVryIXlB33h-pzud7jd3Ka4nRq9lUvwuSict2tJY";//测试环境
        
        $data = array(
                        'first' => array('value' => $template->first_Value, 'color' => $template->first_Color),
                        'ordertape' => array('value' => $template->ordertape_Value, 'color' => $template->ordertape_Color),
                        'ordeID' => array('value' => $template->ordeID_Value, 'color' => $template->ordeID_Color),
	                'remark' => array('value' => $template->remark_Value,'color' => $template->remark_Color),
                    );
        
        $jsonparam  = array(
                            'touser' => $template->touser,
                            'template_id' => $template->template_id,
                            'url' => $template->url,
                            'topcolor' => $template->topcolor,
                            'data' => $data,
                        );
        
        $messageContent = json_encode($jsonparam,JSON_UNESCAPED_UNICODE);
        return parent::send_TemplateMessage($messageContent);
        
    }
    
    
}
