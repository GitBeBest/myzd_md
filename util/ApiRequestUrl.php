<?php
namespace app\util;

class ApiRequestUrl {

    private $hostArray = [
        "http://md.mingyizhudao.com" => "http://crm560.mingyizd.com",
        "http://md.dev.mingyizd.com" => "http://crm.dev.mingyizd.com",
        "http://mdapi.mingyizhudao.com" => "http://crm560.mingyizd.com"
    ];

    private $admin_sales_booking_create = '/api/adminbooking';
    private $doctor_task = '/api/taskuserdoctor';
    private $patientMr_task = '/api/taskpatientmr';
    private $doctor_accept = '/api/doctoraccept';
    private $pay = '/api/tasksalseorder';
    private $da_task = '/api/taskpatientda';
    private $finished = '/api/operationfinished';
    private $commonweal = '/api/taskcommonwealdoctor';

    private $host_info = "http://crm.dev.mingyizd.com";
    private $url_cancel = "/api/cancelbooking";
    private $url_success = "/weixinpub/Sendtempmessage/Paysuccess";
    private $url_unPaid = "/weixinpub/Sendtempmessage/Unpaid";
    private $url_update_status = "/weixinpub/Sendtempmessage/Updatestatus";
    private $url_order_notice = "/weixinpub/Sendtempmessage/Ordernotice";
    private $url_review_notice = "/weixinpub/Sendtempmessage/Reviewnotice";

    private function getHostInfo() {
        $hostInfo = getHostInfo();
        if (isset($this->hostArray[$hostInfo])) {
            return $this->hostArray[$hostInfo];
        } else {
            return $this->host_info;
        }
    }

    public function getUrl($url) {
        return $this->getHostInfo() . $url;
    }

    public function getUrlAdminSalesBookingCreate() {
        return $this->getUrl($this->admin_sales_booking_create);
    }

    public function getUrlDoctorInfoTask() {
        return $this->getUrl($this->doctor_task);
    }

    public function getUrlPatientMrTask() {
        return $this->getUrl($this->patientMr_task);
    }

    public function getUrlDoctorAccept() {
        return $this->getUrl($this->doctor_accept);
    }

    public function getUrlDaTask() {
        return $this->getUrl($this->da_task);
    }

    public function getUrlPay() {
        return $this->getUrl($this->pay);
    }

    public function getUrlFinished() {
        return $this->getUrl($this->finished);
    }

    public function getUrlCommonweal() {
        return $this->getUrl($this->commonweal);
    }

    public function getUrlCancel() {
        return $this->getUrl($this->url_cancel);
    }

    //微信接口
    public function paySuccess() {
        return getHostInfo() . $this->url_success;
    }

    public function unPaid() {
        return getHostInfo() . $this->url_unPaid;
    }

    public function updateStatus() {
        return getHostInfo() . $this->url_update_status;
    }

    public function orderNotice() {
        return getHostInfo() . $this->url_order_notice;
    }

    public function reviewNotice() {
        return getHostInfo() . $this->url_review_notice;
    }

    //模拟发送get请求
    public function send_get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    //模拟发送post请求
    public static function send_post($url, $post_data = '', $timeout = 600) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($post_data != '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }

}
