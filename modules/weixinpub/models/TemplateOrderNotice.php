<?php


/**
 * 新订单提醒
 *
 * @author Administrator
 */
class TemplateOrderNotice extends WechatTemplate{
    
    //标题
    private $first_Value;
    
    private $first_Color;
    
    //提交时间
    private $keyword1_Value;
    
    private $keyword1_Color;
    
    //订单类型
    private $keyword2_Value;
    
    private $keyword2_Color;
    
    //订单状态
    private $keyword3_Value;
    
    private $keyword3_Color;
    
    //订单来源
    private $keyword4_Value;
    
    private $keyword4_Color;
    
    //订单详情
    private $keyword5_Value;
    
    private $keyword5_Color;
    
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
        $template->template_id = "SKly3kz7E74_7A5udMm_TIYh_jnkKiRXR33NgC8KssY";
        //$template->template_id = "CY-Lbi6GZWWJbNtrqOGoHm9jl32-jczr7C_JZ9_zLKg";//测试环境
        
        $data = array(
                        'first' => array('value' => $template->first_Value, 'color' => $template->first_Color),
                        'keyword1' => array('value' => $template->keyword1_Value, 'color' => $template->keyword1_Color),
                        'keyword2' => array('value' => $template->keyword2_Value, 'color' => $template->keyword2_Color),
                        'keyword3' => array('value' => $template->keyword3_Value, 'color' => $template->keyword3_Color),
                        'keyword4' => array('value' => $template->keyword4_Value, 'color' => $template->keyword4_Color),
                        'keyword5' => array('value' => $template->keyword5_Value, 'color' => $template->keyword5_Color),
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
