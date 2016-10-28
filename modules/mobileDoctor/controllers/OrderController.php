<?php
namespace app\modules\mobileDoctor\controllers;

use yii\web\HttpException;

class OrderController extends MobiledoctorController {
    /**
     * @throws CHttpException
     */
    public function actionView() {
        $refNo = $_GET['refNo'];
        if (empty($refNo)) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        $apiSvc = new ApiViewSalesOrder($refNo);
        $output = $apiSvc->loadApiViewData();
        $returnUrl = $this->getReturnUrl("/mobiledoctor/order/view");

        $isInvalid = false;
        if (isset($output->results)) {
            $salesOrder = new SalesOrder();
            $orderTypeString = $salesOrder->getOptionsOrderType();
            if ($output->results->salesOrder->orderType != $orderTypeString[SalesOrder::ORDER_TYPE_DEPOSIT]) {
                $isInvalid = true;
                $salesOrder = $salesOrder->getByAttributes(array('is_paid' => 0, 'ref_no' =>$refNo));
                if (isset($salesOrder->date_invalid)) {
                    strtotime($salesOrder->date_invalid) > time() && $isInvalid = false;
                }
                else {
                    $isInvalid = false;
                }
            }
        }
        
        if ($output->status == 'ok' && $this->isUserAgentWeixin()) {
            $requestUrl = Yii::app()->request->hostInfo . '/weixin/pay.php?' . http_build_query($_GET);
            $data = $output->results;

            // store order data in session, used by wx-pub-pay.

            if (isset($data->booking) && isset($data->booking->id)) {
                $returnUrl = $this->createAbosluteUrl('patientbooking/view', array('id' => $data->booking->id));
            } else {
                $returnUrl = $this->createAbsoluteUrl('doctor/view');
            }
            $data->returnUrl = $returnUrl;
            Yii::app()->session['order.data'] = $data;
            //Yii::app()->session['order.returnUrl'] = $returnUrl;
            //  var_dump(Yii::app()->session['order.returnUrl']);
            //  var_dump($data->booking);
            $this->redirect($requestUrl);
        } else {
            $this->render('view', array(
                'data' => $output,
                'returnUrl' => $returnUrl,
                'isInvalid' => $isInvalid
            ));
        }
    }

    /**
     * Loads order data from session.
     * Used by wx-pub-pay.
     * @param type $refNo
     */
    public function actionLoadOrderPay($refNo = null) {
        $output = new stdClass();
        if (isset(Yii::app()->session['order.data'])) {
            $data = Yii::app()->session['order.data'];
            $order = $data->salesOrder;
            $order->returnUrl = $data->returnUrl;
            //    unset(Yii::app()->session['order.data']);
            //    $output->returnUrl = Yii::app()->session['order.returnUrl'];            
            //    unset(Yii::app()->session['order.returnUrl']);
            if ($order->refNo != $refNo) {
                $output->status = 'no';
                $output->data = 'no data';
                $output->error = 'invalid request';
            } else {
                // get weixin openid.
                $wxMgr = new WeixinpubManager();
                $openid = $wxMgr->getStoredOpenId();
                $order->openid = $openid;
                $output->status = 'ok';
                $output->data = $order;

                $apiSvc = new ApiViewSalesOrder($refNo);
                $apiSvcoutput = $apiSvc->loadApiViewData();
                $isInvalid = false;
                if (isset($apiSvcoutput->results)) {
                    $salesOrder = new SalesOrder();
                    $orderTypeString = $salesOrder->getOptionsOrderType();
                    if ($apiSvcoutput->results->salesOrder->orderType != $orderTypeString[SalesOrder::ORDER_TYPE_DEPOSIT]) {
                        $salesOrder = $salesOrder->getByAttributes(array('ref_no' =>$refNo));
                        if ($salesOrder->is_paid != 1) {
                            $isInvalid = true;
                            if (isset($salesOrder->date_invalid)) {
                                strtotime($salesOrder->date_invalid) > time() && $isInvalid = false;
                            }
                            else {
                                $isInvalid = false;
                            }
                        }
                    }
                }

                $output->isInvalid = $isInvalid;
            }
            // exit;
        } else {
            $output->status = 'no';
            $output->data = 'no data';
            $output->error = 'invalid refNo';
        }
        $this->renderJsonOutput($output);
    }

    //支付单详情
    public function actionOrderView($bookingid) {
        $apiSvc = new ApiViewBookOrder($bookingid);
        $output = $apiSvc->loadApiViewData();

        if (isset($_SERVER['HTTP_REFERER'])) {
            $sessionName = 'orderReferer_' . $output->results->booking->refNo;
            if(preg_match('/^.+(\/mobiledoctor\/patientbooking\/create\/)+.+$/', $_SERVER['HTTP_REFERER']) !== 0) {
                //一次性通过流程到达支付详情时作一个标记
                Yii::app()->session[$sessionName] = true;
            }
            else {
                if(is_null(Yii::app()->session[$sessionName]) === false) unset(Yii::app()->session[$sessionName]);
            }
        }

        $this->render('orderView', array(
            'data' => $output,
        ));
    }

    //分批支付订单
    public function actionPayOrders($bookingId, $orderType) {
        $apiSvc = new ApiViewPayOrders($bookingId, $orderType);
        $output = $apiSvc->loadApiViewData();
        $this->render('payOrders', array(
            'data' => $output
        ));
    }

    public function actionPayResult($paymentcode) {
        $payment = SalesPayment::model()->getByAttributes(array('uid' => $paymentcode), array('paymentOrder'));
        $order = $payment->paymentOrder;
        if ($order === NULL) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        //微信推送信息
        $pbooking = PatientBooking::model()->getById($order->bk_id);
        $wxMgr = new WeixinManager();
        $open = $wxMgr->loadByUserId($pbooking->getCreatorId());
        if (isset($open)) {
            //参数构造
            $url = "http://md.mingyizhudao.com/mobiledoctor/order/orderView/bookingid/" . $pbooking->getId();
            $first_Value = '订单支付成功！名医助手将会尽快安排专家前去会诊。';
            if ($order->order_type == SalesOrder::ORDER_TYPE_DEPOSIT) {
                if (strIsEmpty($pbooking->getExpectedDoctor())) {
                    $first_Value = '您已成功提交预约！名医助手将尽快与您联系确认信息';
                } else {
                    $first_Value = '您已成功预约' . $pbooking->getExpectedDoctor() . '医生！名医助手将尽快与您联系确认信息。';
                }
            }
            $params = array("touser" => $open->getOpenId(), "url" => $url, "first_Value" => $first_Value, "keyword1_Value" => $pbooking->getPatientName(),
                "keyword2_Value" => $order->getFinalAmount(), "keyword3_Value" => $order->getDateClose('Y年m月d日 H:i'));
            $wxMgr = new WeixinManager();
            $wxMgr->paySuccess($params);
        }
        //支付成功 生成task提醒
//        $apiurl = new ApiRequestUrl();
//        $url = $apiurl->getUrlPay() . "?refno=" . $order->getRefNo();
//        $this->send_get($url);

        //一次性通过流程进行支付的订单在数据库中作标记
        if (Yii::app()->session['orderReferer_' . $pbooking->getRefNo()] === true) {
           $adminBookingManager = new AdminBookingManager();
           $adminBookingManager->setDockingCase(1, $pbooking->getRefNo());
        }

        $this->show_header = true;
        $this->show_footer = false;
        $this->show_baidushangqiao = false;
        $this->render('payResult', array('order' => $order));
    }

}
