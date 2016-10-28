<?php

/**
 * 订单状态更新
 *
 * @author zhongtw
 */
class TemplateUpdateStatus extends WechatTemplate{
    
    //标题
    private $first_Value;
    
    private $first_Color;
    
    //订单编号
    private $keyword1_Value;
    
    private $keyword1_Color;
    
    //订单状态
    private $keyword2_Value;
    
    private $keyword2_Color;
    
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
        $template->template_id = "CgduVW1vBv2BUWKd1qEmSCwoEAJf7SRaulxbZ-ciEC0";
        //$template->template_id = "KkXgtb7F_cklCUQWmtrMfs716cYbR1IqixldYrSQ2Pk";//测试环境
        
        $data = array(
                        'first' => array('value' => $template->first_Value, 'color' => $template->first_Color),
                        'keyword1' => array('value' => $template->keyword1_Value, 'color' => $template->keyword1_Color),
                        'keyword2' => array('value' => $template->keyword2_Value, 'color' => $template->keyword2_Color),
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
