<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WechattestController
 *
 * @author Administrator
 */
define("MCH_ID", "123");//
define("API_KEY", "456");
class WechattestController extends WeixinpubController{
    
    
    public function actionTest5() {
        echo MCH_ID;
        echo "</br>";
        echo API_KEY;
    }
    
    //增加数据
    public function actionTest2(){
        
        $tongweiTest = new TongweiTest();
        $tongweiTest->username = "username1";
        $tongweiTest->password = "password2";
        $tongweiTest->backtime = date("Y-m-d H:i:s", time()); 
        if($tongweiTest->save()){
            var_dump($tongweiTest);            
            exit();
        }else{
           var_dump($tongweiTest->getErrors());  
           exit();
        };

    } 
    
    
    //查询数据操作
    public function actionTest3(){
        $tongweiTest = new TongweiTest();
        $criteria = new CDbCriteria;
        $criteria->select = 'username';
        $criteria->addCondition("username = 'username1'");
        $criteria->addCondition("id = 1");
        $resurt = $tongweiTest->findAll($criteria);
        echo $resurt['id'];
        //echo $resurt['username'];      
    }
    
    
    
    public function actionTest() {
        $this->render('test001');
    }
    
    public function actionTest1() {
        $wechatMsgRecord = new WechatMsgRecord();      
        $wechatMsgRecord->from_username = "1234";
        $wechatMsgRecord->to_username = "324";
        $wechatMsgRecord->send_content = "4321";
        $wechatMsgRecord->send_status = "1234";
        if($wechatMsgRecord->save()){
            echo "success";
        }else{
           var_dump($wechatMsgRecord->getErrors());  exit();
        };
    }
}
