<?php
namespace app\models\booking;
use app\models\base\BaseActiveRecord;
use app\models\doctor\Doctor;
use app\models\doctor\ExpertTeam;
use app\models\hospital\Hospital;
use app\models\hospital\HospitalDepartment;
use app\models\region\RegionCity;
use app\models\sales\SalesOrder;
use app\models\user\User;
use app\util\StatCode;

/**
 * Class Booking
 * @package app\models\booking
 *
 * @property integer $id
 * @property string $ref_no
 * @property integer $doctor_user_id
 * @property integer $doctor_user_name
 * @property string $mobile
 * @property string $contact_name
 * @property string $contact_email
 * @property integer $bk_status
 * @property integer $bk_type
 * @property integer $doctor_id
 * @property string $doctor_name
 * @property integer $expteam_id
 * @property string $expteam_name
 * @property integer $city_id
 * @property integer $hospital_id
 * @property string $hospital_name
 * @property integer $hp_dept_id
 * @property string $hp_dept_name
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $date_start
 * @property integer $user_id
 * @property string $date_end
 * @property string $appt_date
 * @property integer $is_deposit_paid
 * @property string $user_agent
 * @property string $remark
 * @property string $is_corporate
 * @property string $corporate_name
 * @property string $corp_staff_rel
 * @property string $submit_via
 * @property integer $is_vendor
 * @property integer $vendor_id
 * @property integer $vendor_site
 * @property integer $vendor_trade_no
 * @property integer $is_commonweal
 * @property integer $booking_service_id
 * @property integer $doctor_accept
 * @property string  $doctor_opinion
 * @property string  $cs_explain
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Doctor $doctor
 * @property Hospital $hospital
 * @property HospitalDepartment $hospitalDepartment
 * @property ExpertTeam $expertTeam
 * @property RegionCity $city
 * @property User $user
 */
class Booking extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'booking';
    }

    //预约页面分页行数
    const BOOKING_PAGE_SIZE = 10;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['ref_no', 'mobile', 'bk_status', 'bk_type'], 'required'],
            [['doctor_user_id', 'user_id', 'bk_status', 'bk_type', 'doctor_id', 'expteam_id', 'city_id', 'hospital_id', 'hp_dept_id', 'is_corporate', 'doctor_accept'], 'number', 'integerOnly' => true],
            [['ref_no'], 'string', 'max'=>14, 'min' => 14],
            [['mobile'], 'string', 'max' => 14, 'min' => 14],
            [['contact_name', 'doctor_name', 'expteam_name', 'hospital_name', 'hp_dept_name', 'disease_name', 'corporate_name', 'corp_staff_rel', 'user_agent'], 'string', 'max' => 50],
            [['contact_email'], 'string', 'max' => 100],
            [['disease_detail', 'doctor_opinion'], 'string', 'max' => 1000],
            [['remark'], 'string', 'max' => 500],
            [['submit_via'], 'string', 'max' => 10],
            [['date_start', 'date_end', 'appt_date', 'date_created', 'date_updated', 'date_deleted', 'user_agent','is_commonweal','booking_service_id'], 'safe'],
        ];
    }

    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['doctor_id' => 'id']);
    }

    public function getExpertTeam() {
        return $this->hasOne(ExpertTeam::className(), ['expteam_id' => 'id']);
    }

    public function getHospital() {
        return $this->hasOne(Hospital::className(), ['hospital_id' => 'id']);
    }

    public function getHospitalDepartment() {
        return $this->hasOne(HospitalDepartment::className(), ['hp_dept_id' => 'id']);
    }

    public function getRegionCity() {
        return $this->hasOne(RegionCity::className(), ['city_id' => 'id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function getSalesOrder() {
        return $this->hasMany(SalesOrder::className(), ['id' => 'bk_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => '预约号',
            'user_id' => '用户',
            'mobile' => '手机号',
            'contact_name' => '患者姓名',
            'contact_email' => '邮箱',
            'bk_status' => '状态',
            'bk_type' => '种类',
            'doctor_id' => '医生',
            'doctor_name' => '医生姓名',
            'expteam_id' => '专家团队',
            'expteam_name' => '专家团队',
            'city_id' => '城市',
            'hospital_id' => '医院',
            'hospital_name' => '医院名称',
            'hp_dept_id' => '科室',
            'hp_dept_name' => '科室名称',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病情',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'appt_date' => 'Appt Date',
            'user_agent' => '数据来源',
            'remark' => 'Remark',
            'is_corporate' => '是否是企业用户',
            'corporate_name' => '企业名称',
            'corp_staff_rel' => '与患者的关系',
            'submit_via' => 'Submit Via',
            'date_created' => '创建日期',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'expertBooked' => '所约专家'
        );
    }

    /**
     * 根据bookingId查询数据
     * @param array $bookingIds
     * @param array $attr
     * @param string $with
     * @param array $options
     * @return type
     */
    public function getAllByIds($bookingIds, $attr = null, $with = null, $options = null) {
        $result = $this->find()->alias('t')
            ->joinWith($with)
            ->where(['t.date_deleted' => null])
            ->andWhere(['in', 't.id', $bookingIds])
            ->all();

        return $result;
    }

    public function beforeValidate() {
        $this->createRefNumber();
        return parent::beforeValidate();
    }

    public function beforeSave($insert) {
        return parent::beforeSave(true);
    }

    public function getByRefNo($ref_no) {
        return $this->getByAttributes(array('ref_no' => $ref_no));
    }

    public function getByIdAndUserId($id, $userId, $with = null) {
        return $this->getByAttributes(array('id' => $id, 'user_id' => $userId), $with);
    }

    public function getByIdAndUser($id, $userId, $mobile, $with = null) {
        $result = $this->find()->alias('t')
            ->where(['and', 't.id = :id', ['or', 't.user_id=:userId', 't.mobile=:mobile']])
            ->addParams([':id' => $id, ':userId' => $userId, ':mobile'=>$mobile])
            ->joinWith($with)
            ->distinct()
            ->all();
        return $result;
    }

    public function getAllByUserIdOrMobile($userId, $mobile, $with = null, $options = null) {
        $model = $this->find()->alias('t')
            ->join('LEFT JOIN', 'comment as c', ['t.`id`' => 'c.`bk_id`', 'c.`bk_type`' => StatCode::TRANS_TYPE_BK])
            ->distinct()
            ->where(['t.user_id' => $userId])
            ->orWhere(['t.mobile' => $mobile])
            ->andWhere(['t.date_deleted' => null]);
        if (isset($with) && is_array($with))
            $model->joinWith($with);
        if (isset($options['offset']))
            $model->offset($options['offset']);
        if (isset($options['limit']))
            $model->limit($options['limit']);
        if (isset($options['order']))
            $model->orderBy($options['order']);

        $result = $model->all();

        return $result;
    }

    //根据上级医生用户id查询患者预约
    public function getAllByDoctorUserId($doctorUserId) {
        return $this->getAllByAttributes(array('doctor_user_id' => $doctorUserId));
    }

    /**
     * 根据上级医生用户id和bookingId查询预约详情
     * @param $id
     * @param $doctorUserId
     * @return $this
     */
    public function getByIdAndDoctorUserId($id, $doctorUserId) {
        return $this->getByAttributes(array('id' => $id, 'doctor_user_id' => $doctorUserId));
    }

    public function getCountByUserIdOrMobile($userId, $mobile) {
        $count = $this->find()->where(['user_id' => $userId])
            ->orWhere(['mobile' => $mobile])
            ->andWhere(['date_deleted' => null])
            ->count();

        return $count;
    }

    public function setIsCorporate($v = 1) {
        $this->is_corporate = $v;
    }

    public function getOptionsStatus() {
        return StatCode::getOptionsBookingStatus();
    }

    public function getOptionsBookingType() {
        return StatCode::getOptionsBookingType();
    }

    public function getBookingTypeText() {
        $options = $this->getOptionsBookingType();
        if (isset($options[$this->bk_type])) {
            return $options[$this->bk_type];
        } else {
            return "未知";
        }
    }

    public static function getOptionsBookingStatus() {
        return array(
            StatCode::BK_STATUS_NEW => '待付预约金',
            StatCode::BK_STATUS_PROCESSING => '安排专家中',
//            StatCode::BK_STATUS_CONFIRMED_DOCTOR => '专家已确认',
//            StatCode::BK_STATUS_PATIENT_ACCEPTED => '患者已接受',
            StatCode::BK_STATUS_SERVICE_UNPAID => '待付咨询费',
            StatCode::BK_STATUS_SERVICE_PAID => '待评价',
            StatCode::BK_STATUS_PROCESS_DONE => '跟进结束',
            StatCode::BK_STATUS_DONE => '已评价',
            StatCode::BK_STATUS_INVALID => '无效',
            StatCode::BK_STATUS_CANCELLED => '已取消'
        );
    }

    public function getBookingStatus() {
        $options = self::getOptionsBookingStatus();
        if (isset($options[$this->bk_status])) {
            return $options[$this->bk_status];
        } else {
            return null;
        }
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

    public function hasBookingTarget() {
        return (empty($this->booking_target) === false);
    }

    /*     * ****** Private Methods ******* */

    private function createRefNumber() {
        if ($this->isNewRecord) {
            $flag = true;
            while ($flag) {
                $refNumber = $this->getRefNumberPrefix() . date("ymd") . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
                if ($this->findOne(['ref_no' => $refNumber]) == false) {
                    $this->ref_no = $refNumber;
                    $flag = false;
                }
            }
        }
    }

    /**
     * Return ref_no prefix charactor based on bk_type
     * default 'AA' is an eception charactor
     * @return string
     */
    private function getRefNumberPrefix() {
        switch ($this->bk_type) {
            case StatCode::BK_TYPE_DOCTOR :
                return "DR";
            case StatCode::BK_TYPE_EXPERT_TEAM :
                return "ET";
            case StatCode::BK_TYPE_QUICK_BOOK :
                return "QB";
            case StatCode::BK_TYPE_DEPT :
                return "HP";
            default:
                return "AA";
        }
    }

    /*     * ****** Accessors ******* */

    public function getRefNo() {
        return $this->ref_no;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getContactName() {
        return $this->contact_name;
    }

    public function getBkStatus() {
        return $this->getBookingStatus();
    }

    public function setBkStatus($v) {
        $this->bk_status = $v;
    }

    public function getBookingType() {
        return StatCode::getBookingType($this->bk_type);
    }

    public function setDoctorAccept($v) {
        $this->doctor_accept = $v;
    }

    public function setDoctorOpinion($v) {
        $this->doctor_opinion = $v;
    }

    public function getDoctorName() {
        if (strIsEmpty($this->doctor_name) === false) {
            return $this->doctor_name;
        } elseif (isset($this->doctor_id) && $this->doctor !== null) {
            return $this->doctor->getName();
        } else {
            return '';
        }
    }

    public function getExpertBooked() {
        if ($this->bk_type == StatCode::BK_TYPE_EXPERT_TEAM) {
            return $this->expertTeam;
        } elseif ($this->bk_type == StatCode::BK_TYPE_DOCTOR) {
            return $this->doctor;
        } else {
            return null;
        }
    }

    public function getExpertNameBooked() {
        if ($this->getExpertBooked() !== null) {
            return $this->getExpertBooked()->getName();
        } else {
            return '';
        }
    }
    public function getExpertTeamId() {
        return $this->expteam_id;
    }


    public function getHospitalName() {
        if (strIsEmpty($this->hospital_name) === false) {
            return $this->hospital_name;
        } elseif (isset($this->hospital_id) && $this->hospital !== null) {
            return $this->hospital->getName();
        } else {
            return '';
        }
    }

    public function getHpDeptName() {
        if (strIsEmpty($this->hp_dept_name) === false) {
            return $this->hp_dept_name;
        } elseif (isset($this->hp_dept_id) && $this->hospitalDepartment !== null) {
            return $this->hospitalDepartment->getName();
        } else {
            return '';
        }
    }

    public function getDiseaseName() {
        return $this->disease_name;
    }

    public function getDiseaseDetail($nText = true) {
        return $this->getTextAttribute($this->disease_detail, $nText);
    }

    public function getDateStart($format = null) {
        return $this->getDateAttribute($this->date_start, $format);
    }

    public function getDateEnd($format = null) {
        return $this->getDateAttribute($this->date_end, $format);
    }

    public function getApptDate($format = null) {
        return $this->getDatetimeAttribute($this->appt_date, $format);
    }

    public function getRemark($nText = true) {
        return $this->getTextAttribute($this->remark, $nText);
    }

    public function getIsCorporate() {
        return $this->is_corporate;
    }

    public function getCorporateName() {
        return $this->corporate_name;
    }

    public function getCorpStaffRef() {
        return $this->corp_staff_rel;
    }

    public function getUserAgent() {
        return $this->user_agent;
    }

    public function getDoctorUserId() {
        return $this->doctor_user_id;
    }

    public function getExpectedDoctor() {
        return $this->doctor_name;
    }

}
