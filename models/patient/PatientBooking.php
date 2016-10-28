<?php
namespace app\models\patient;
use app\models\base\BaseActiveRecord;
use app\models\patient\PatientInfo;
use app\models\sales\SalesOrder;
use app\models\user\User;
use app\util\StatCode;

/**
 * Class PatientBooking
 * @package app\models\patient
 *
 * @property integer $id
 * @property integer $patient_id
 * @property String $patient_name
 * @property integer $doctor_id
 * @property String $doctor_name
 * @property integer $creator_id
 * @property String $creator_name
 * @property integer $status
 * @property integer $travel_type
 * @property string $date_start
 * @property string $date_end
 * @property string $detail
 * @property integer $is_deposit_paid
 * @property string $ref_no
 * @property string $user_agent
 * @property string $expected_doctor
 * @property string $expected_hospital
 * @property string $expected_dept
 * @property string $doctor_opinion
 * @property string $appt_date
 * @property string $date_confirm
 * @property string $remark
 * @property string $doctor_accept
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class PatientBooking extends BaseActiveRecord {

    const BK_STATUS_NEW = 1;         // 待支付
    const BK_STATUS_PROCESSING = 2;   // 安排中    
    //const BK_STATUS_CONFIRMED_DOCTOR = 3;   // 已确认专家
    //const BK_STATUS_PATIENT_ACCEPTED = 4;   // 患者已接受
    const BK_STATUS_SERVICE_UNPAID = 5;   //待确认
    const BK_STATUS_SERVICE_PAID = 6;   // 待完成
    //const BK_STATUS_INVALID = 7;        // 失效的
    const BK_STATUS_SURGERY_DONE = 8;        // 已完成
    const BK_STATUS_CANCELLED = 9;          // 已取消

    //const BK_STATUS_BEDONE = 11;             //待完成

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'patient_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['patient_id', 'creator_id', 'status', 'travel_type'], 'required'],
            [['patient_id', 'creator_id', 'doctor_id', 'status', 'travel_type', 'operation_finished'], 'number', 'integerOnly' => true],
            [['ref_no'], 'string', 'max' => 14, 'min' => 14],
            [['user_agent', 'doctor_name', 'patient_name', 'creator_name'], 'string', 'max' => 20],
            [['expected_doctor'], 'max' => 200],
            [['expected_dept', 'expected_hospital'], 'string', 'max' => 200],
            [['detail'], 'string', 'max' => 1000],
            [['remark', 'cs_explain', 'doctor_opinion'], 'string', 'max' => 500],
            [['expected_dept', 'expected_hospital', 'expected_doctor', 'appt_date', 'date_confirm', 'date_created', 'date_updated', 'date_deleted', 'date_start', 'date_end'], 'safe']
        ];
    }

    public function getPatientInfo() {
        return $this->hasOne(PatientInfo::className(), ['id' => 'patient_id']);
    }

    public function getCreator() {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    public function getDoctor() {
        return $this->hasOne(User::className(), ['id' => 'doctor_id']);
    }

    public function getSalesOrder() {
        return $this->hasMany(SalesOrder::className(), ['bk_id' => 'id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => '预约号',
            'patient_id' => '患者',
            'patient_name' => '患者',
            'creator_id' => '创建者',
            'creator_name' => '创建者',
            'doctor_id' => '预约医生',
            'doctor_name' => '预约医生',
            'status' => '状态',
            'travel_type' => '出行方式',
            'date_start' => '开始日期',
            'date_end' => '结束日期',
            'detail' => '细节',
            'is_deposit_paid' => '是否支付定金',
            'appt_date' => '最终预约日期',
            'date_confirm' => '预约确认日期',
            'remark' => '备注',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function beforeValidate() {
        $this->createRefNumber();
        return parent::beforeValidate();
    }

    //查询创建者旗下所有的患者
    public function getAllByCreatorId($creatorId, $status, $attributes = '*', $with = null, $options = null) {
        if ($status == '0') {
            $array = array('t.creator_id' => $creatorId);
        } else {
            $array = array('t.creator_id' => $creatorId, 't.status' => $status);
        }
        return $this->getAllByAttributes($array, $with, $options);
    }

    //查询该创建者旗下的患者信息
    public function getByIdAndCreatorId($id, $creatorId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('id' => $id, 'creator_id' => $creatorId), $with);
    }

    //根据外键查询booking
    public function getByPatientId($patientId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('patient_id' => $patientId), $with);
    }

    //查询预约该医生的患者列表
    public function getAllByDoctorId($doctorId, $attributes = '*', $with = null, $options = null) {
        return $this->getAllByAttributes(array('t.doctor_id' => $doctorId), $with, $options);
    }

    //查询预约该医生的患者详细信息
    public function getByIdAndDoctorId($id, $doctorId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('id' => $id, 'doctor_id' => $doctorId), $with);
    }


    public function getId() {
        return $this->id;
    }

    public function getRefNo() {
        return $this->ref_no;
    }

    public function getPatientId() {
        return $this->patient_id;
    }

    public function getCreatorId() {
        return $this->creator_id;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function getCreatorName() {
        return $this->creator_name;
    }

    public function getPatientName() {
        return $this->patient_name;
    }

    public function getDoctorName() {
        return $this->doctor_name;
    }

    public function getOptionsBkStatus() {
        return array(
            self::BK_STATUS_NEW => '待支付',
            self::BK_STATUS_PROCESSING => '安排中',
            //self::BK_STATUS_CONFIRMED_DOCTOR => '已确认专家',
            //    self::BK_STATUS_PATIENT_ACCEPTED => '患者已接受',
            self::BK_STATUS_SERVICE_UNPAID => '待确认',
            self::BK_STATUS_SERVICE_PAID => '待完成',
            self::BK_STATUS_SURGERY_DONE => '已完成',
            self::BK_STATUS_CANCELLED => '已取消',
                //self::BK_STATUS_BEDONE => '待完成',
                //self::BK_STATUS_INVALID => '失效的'
        );
    }

    public function getStatus($text = true) {
        if ($text) {
            $options = self::getOptionsBkStatus();
            if (isset($options[$this->status])) {
                return $options[$this->status];
            } else {
                return StatCode::ERROR_UNKNOWN;
            }
        } else {
            return $this->status;
        }
    }

    public function getTitleBkStatus() {
        return array(
            self::BK_STATUS_NEW => '请您支付手术预约金',
            self::BK_STATUS_PROCESSING => '当前状态:安排专家中',
            self::BK_STATUS_SERVICE_UNPAID => '当前状态:待支付平台咨询费',
            //self::BK_STATUS_SERVICE_PAIDED => '当前状态:待上传出院小结',
            self::BK_STATUS_CANCELLED => '当前状态:已取消预约单',
            self::BK_STATUS_SURGERY_DONE => '感谢你协助完成了该例手术!',
            self::BK_STATUS_SERVICE_PAID => '当前状态:待确认手术完成',
        );
    }

    public function getStatusTitle() {
        $options = self::getTitleBkStatus();
        if (isset($options[$this->status])) {
            return $options[$this->status];
        } else {
            return StatCode::ERROR_UNKNOWN;
        }
    }

    public function getTravelType($text = true) {
        if ($text) {
            return StatCode::getBookingTravelType($this->travel_type);
        } else {
            return $this->travel_type;
        }
    }

    public function getDateStart($format = null) {
        return $this->getDateAttribute($this->date_start, $format);
    }

    public function getDateEnd($format = null) {
        return $this->getDateAttribute($this->date_end, $format);
    }

    public function getDetail($nText = true) {
        return $this->getTextAttribute($this->detail, $nText);
    }

    public function getApptdate($format = null) {
        return $this->getDateAttribute($this->appt_date, $format);
    }

    public function getDateConfirm($format = null) {
        return $this->getDatetimeAttribute($this->date_confirm, $format);
    }

    public function getRemark($nText = true) {
        return $this->getTextAttribute($this->remark, $nText);
    }

    public function getDoctorAccept($nText = false) {
        if ($nText) {
            return $this->doctor_accept == 1 ? "已接受手术" : "已拒接手术";
        }
        return $this->doctor_accept;
    }

    public function getDoctorOpinion($nText = true) {
        return $this->getTextAttribute($this->doctor_opinion, $nText);
    }

    public function getCsExplain($nText = true) {
        return $this->getTextAttribute($this->cs_explain, $nText);
    }

    public function getIsDepositPaid($text = false) {
        if ($text) {
            return StatCode::getPaymentStatus($this->is_deposit_paid);
        } else {
            return $this->is_deposit_paid;
        }
    }

    public function getUserAgent() {
        return $this->user_agent;
    }

    public function getExpectedDoctor() {
        return $this->expected_doctor;
    }

    public function getExpectedHospital() {
        return $this->expected_hospital;
    }

    public function getExpectedDept() {
        return $this->expected_dept;
    }

    public function setStatus($v) {
        $this->status = $v;
    }

    public function setCreatorId($v) {
        $this->creator_id = $v;
    }

    public function setPatientId($v) {
        $this->patient_id = $v;
    }

    public function setDoctorId($v) {
        $this->doctor_id = $v;
    }

    public function setCreatorName($v) {
        $this->creator_name = $v;
    }

    public function setPatientName($v) {
        $this->patient_name = $v;
    }

    public function setDoctorName($v) {
        $this->doctor_name = $v;
    }

    public function setDoctorAccept($v) {
        $this->doctor_accept = $v;
    }

    public function setDoctorOpinion($v) {
        $this->doctor_opinion = $v;
    }

    private function createRefNumber() {
        if ($this->isNewRecord) {
            $flag = true;
            while ($flag) {
                $refNumber = 'PB' . date("ymd") . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
                $model = $this->findOne(['ref_no' => $refNumber]);
                if (empty($model)) {
                    $this->ref_no = $refNumber;
                    $flag = false;
                }
            }
        }
    }

}
