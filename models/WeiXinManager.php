<?php
namespace app\models;

use app\models\user\WeiXinPubOpenid;
use yii\helpers\Json;
use app\util\ApiRequestUrl;
use app\models\patient\PatientBooking;

class WeiXinManager {

    public function loadByUserId($userId) {
        $model = new WeiXinPubOpenid();
        return $model->getByAttributes(['user_id' => $userId]);
    }

    //支付成功推送信息
    public function paySuccess($values) {
        $params = array(
            "touser" => $values['touser'],
            "url" => $values['url'],
            "first_Value" => $values['first_Value'],
            "keyword1_Value" => $values['keyword1_Value'],
            "keyword2_Value" => $values['keyword2_Value'],
            "keyword3_Value" => $values['keyword3_Value'],
            "remark_Value" => "如有疑问请拨打热线400-6277-120。谢谢！"
        );
        $postJson = Json::encode($params);
        $apiRequest = new ApiRequestUrl();
        $apiRequest->send_post($apiRequest->paySuccess(), $postJson);
    }

    /**
     * crm改变订单状态 新增服务费通知
     * @param $bookingId
     * @return array|mixed
     */
    public function unPaid($bookingId) {
        $output = [
            "errcode" => '0',
            "errmsg" => "data error"
        ];
        $model = new PatientBooking();
        $booking = $model->getById($bookingId);
        $open = $this->loadByUserId($booking->getCreatorId());
        if (isset($open)) {
            $url = getHostInfo() . "/mobiledoctor/order/orderView/bookingid/{$bookingId}";
            $doctor = "  会诊专家: " . $booking->getExpectedDoctor();
            if (strIsEmpty($booking->getExpectedDoctor())) {
                $doctor = "";
            }
            $params = array(
                "touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => '您好！您的订单已经处理成功，请尽快完成支付。',
                "ordertape_Value" => $booking->getDateCreated('Y年m月d日 H:i'),
                "orderID_Value" => "订单号：【" . $booking->getRefNo() . "】患者姓名：" . $booking->getPatientName() . $doctor,
                "remark_Value" => "如有疑问请拨打热线400-6277-120。谢谢！"
            );
            $postJson = Json::encode($params);
            $apiRequest = new ApiRequestUrl();
            $output = $apiRequest->send_post($apiRequest->unPaid(), $postJson);
        }
        return $output;
    }

    /**
     * 订单更新
     * @param $values
     * @return mixed
     */
    public function UpdateStatus($values) {
        $params = array("touser" => $values['touser'],
            "url" => $values['url'],
            "first_Value" => $values['first_Value'],
            "keyword1_Value" => $values['keyword1_Value'],
            "keyword2_Value" => $values['keyword2_Value'],
            "remark_Value" => $values['remark_Value']);
        $postJson = Json::encode($params);
        $apiRequest = new ApiRequestUrl();
        return $apiRequest->send_post($apiRequest->updatestatus(), $postJson);
    }

    /**
     * 医生上传完出院小结
     * @param $bookingId
     * @return array|mixed
     */
    public function uploadDr($bookingId) {
        $output = array("errcode" => '0', "errmsg" => "data error");
        $pbooking = PatientBooking::model()->getById($bookingId);
        $open = $this->loadByUserId($pbooking->getCreatorId());
        if (isset($open)) {
            $detail = "邀请医生手术 - " . $pbooking->getExpectedDoctor();
            if (strIsEmpty($pbooking->getExpectedDoctor())) {
                $detail = "暂无";
            }
            $url = getHostInfo() . "/mobiledoctor/order/orderView/bookingid/{$bookingId}";
            $values = array("touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => "您好！您已成功提交出院小结，客服将将尽快对其审核。",
                "keyword1_Value" => $pbooking->getRefNo(),
                "keyword2_Value" => "已上传出院小结",
                "remark_Value" => $detail . "\n如有疑问请拨打热线400-6277-120。谢谢！");
            $output = $this->Updatestatus($values);
        }
        return $output;
    }

    //上级医生回复
    public function doctorOpinion($bookingId, $bkType = StatCode::TRANS_TYPE_PB) {
        $output = array("errcode" => '0', "errmsg" => "data error");
        if ($bkType == StatCode::TRANS_TYPE_PB) {
            $booking = PatientBooking::model()->getById($bookingId);
            $userId = $booking->getDoctorId();
        } else {
            $booking = Booking::model()->getById($bookingId);
            $userId = $booking->getDoctorUserId();
        }
        $open = $this->loadByUserId($userId);
        if (isset($open)) {
            $url = getHostInfo() . "/mobiledoctor/patientbooking/doctorPatientBooking/id/{$bookingId}/type/{$bkType}";
            $detail = "邀请医生手术 - " . $booking->getExpectedDoctor();
            if (strIsEmpty($booking->getExpectedDoctor())) {
                $detail = "暂无";
            }
            $values = array("touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => "您好！感谢您对手术邀请做出答复。",
                "keyword1_Value" => $booking->getRefNo(),
                "keyword2_Value" => $booking->getDoctorAccept(true),
                "remark_Value" => $detail . "\n如有疑问请拨打热线400-6277-120。谢谢！");
            $output = $this->Updatestatus($values);
        }
        return $output;
    }

    //审核类
    public function reviewed($values) {
        $params = array("touser" => $values['touser'],
            "url" => $values['url'],
            "first_Value" => $values['first_Value'],
            "keyword1_Value" => $values['keyword1_Value'],
            "keyword2_Value" => $values['keyword2_Value'],
            "keyword3_Value" => $values['keyword3_Value'],
            "remark_Value" => $values['remark_Value']);
        $postJosn = CJSON::encode($params);
        $apiRequesr = new ApiRequestUrl();
        return $apiRequesr->send_post($apiRequesr->reviewNotice(), $postJosn);
    }

    //出院小结审核结果通知
    public function drNotice($bookingId) {
        $output = array("errcode" => '0', "errmsg" => "data error");
        $with = array('pbCreator');
        $pbooking = PatientBooking::model()->getById($bookingId, $with);
        $open = $this->loadByUserId($pbooking->getCreatorId());
        if (isset($open)) {
            $user = $pbooking->getCreator();
            $url = getHostInfo() . "/mobiledoctor/order/orderView/bookingid/{$bookingId}";
            $values = array("touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => "您好！您上传的出院小结已经通过审核。",
                "keyword1_Value" => $pbooking->getCreatorName(),
                "keyword2_Value" => $user->getMobile(),
                "keyword3_Value" => date('Y年m月d日 H:i'),
                "remark_Value" => "感谢您的医者仁心和辛勤付出。如有疑问请拨打热线400-6277-120。谢谢！");
            $output = $this->reviewed($values);
        }
        return $output;
    }

    //医生信息审核
    public function profileNotice($userId, $type = StatCode::PROFILE_SUCCESS) {
        $output = array("errcode" => '0', "errmsg" => "data error");
        $open = $this->loadByUserId($userId);
        if (isset($open)) {
            $profile = UserDoctorProfile::model()->getByAttributes(array('user_id' => $userId));
            if ($type == StatCode::PROFILE_SUCCESS) {
                $url = getHostInfo() . "/mobiledoctor/doctor/doctorTerms";
                $title = "您好！恭喜您的实名认证已通过审核。";
                $verified = $profile->getDateVerified('Y年m月d日 H:i');
                $remark = "欢迎您使用名医主刀！";
            } else {
                $url = getHostInfo() . "/mobiledoctor/doctor/uploadCert";
                $title = "您好！您实名认证尚未通过，请按照要求提交照片，谢谢！";
                $verified = date('Y年m月d日 H:i');
                $remark = "如有疑问请拨打热线400-6277-120。谢谢！";
            }
            $values = array("touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => $title,
                "keyword1_Value" => $profile->getName(),
                "keyword2_Value" => $profile->getMobile(),
                "keyword3_Value" => $verified,
                "remark_Value" => $remark);
            $output = $this->reviewed($values);
        }
        return $output;
    }

    //订单关联上级医生
    public function bookingtodoctor($bookingId, $bkType = StatCode::TRANS_TYPE_PB) {
        $output = array("errcode" => '0', "errmsg" => "data error");
        if ($bkType == StatCode::TRANS_TYPE_PB) {
            $booking = PatientBooking::model()->getById($bookingId);
            $userId = $booking->getDoctorId();
            $travelType = $booking->getTravelType();
            $status = $booking->getStatus();
            $profile = UserDoctorProfile::model()->getByUserId($booking->getCreatorId());
            $from = $profile->getHospitalName() . $profile->getHpDeptName();
            $detail = $booking->getDetail();
        } else {
            $booking = Booking::model()->getById($bookingId);
            $userId = $booking->getDoctorUserId();
            $travelType = "";
            $status = $booking->getBookingStatus();
            $from = $booking->getHospitalName() . $booking->getHpDeptName();
            $detail = $booking->getDiseaseDetail();
        }
        $open = $this->loadByUserId($userId);
        if (isset($open)) {
            $url = getHostInfo() . "/mobiledoctor/patientbooking/doctorPatientBooking/id/{$bookingId}/type/{$bkType}";
            $params = array("touser" => $open->getOpenId(),
                "url" => $url,
                "first_Value" => "您好！您有新的手术预约单等待处理。",
                "keyword1_Value" => $booking->getDateCreated('Y年m月d日 H:i'),
                "keyword2_Value" => $travelType,
                "keyword3_Value" => $status,
                "keyword4_Value" => $from,
                "keyword5_Value" => $detail,
                "remark_Value" => "如有疑问请拨打热线400-6277-120。谢谢！");
            $postJosn = CJSON::encode($params);
            $apiRequesr = new ApiRequestUrl();
            $output = $apiRequesr->send_post($apiRequesr->orderNotice(), $postJosn);
        }
        return $output;
    }

}
