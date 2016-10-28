<?php

/**
 * This is the model class for table "user_doctor_profile".
 *
 * The followings are the available columns in table 'user_doctor_profile':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $mobile
 * @property integer $gender
 * @property integer $hospital_id
 * @property string $hospital_name
 * @property integer $hp_dept_id
 * @property string $hp_dept_name
 * @property integer $clinical_title
 * @property integer $academic_title
 * @property integer $country_id
 * @property integer $state_id
 * @property string $state_name
 * @property integer $city_id
 * @property string $city_name
 * @property string $date_verified
 * @property integer $verified_by
 * @property string $preferred_patient
 * @property string $date_contracted
 * @property string $date_deleted
 * @property string $date_created
 * @property string $date_updated
 */
namespace app\models\doctor;
use app\models\base\BaseActiveRecord;
use app\models\hospital\Hospital;
use app\models\hospital\HospitalDepartment;
use app\models\region\RegionCity;
use app\models\region\RegionState;
use app\models\user\User;
use app\util\StatCode;
use yii\db\Expression;

/**
 * Class UserDoctorProfile
 * @package app\models\doctor
 *
 * @property integer $user_id
 * @property string $name
 * @property string $clinical_title
 * @property integer $gender
 * @property integer $hospital_id
 * @property integer $hp_dept_id
 * @property string $academic_title
 * @property integer $country_id
 * @property integer $state_id
 * @property integer $city_id
 * @property integer $verified_by
 * @property string $mobile
 * @property string $date_verified
 * @property string $date_deleted
 * @property string $date_updated
 * @property string $date_contracted
 * @property string $preferred_patient
 * @property string $date_terms_doctor
 * @property string $state_name
 * @property string $hospital_name
 * @property integer $is_commonweal
 */
class UserDoctorProfile extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'user_doctor_profile';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['user_id', 'name', 'clinical_title'], 'required'],
            [['user_id', 'gender', 'hospital_id', 'hp_dept_id', 'clinical_title', 'academic_title', 'country_id', 'state_id', 'city_id'], 'number', 'integerOnly' => true],
            [['name', 'hospital_name', 'hp_dept_name', 'state_name', 'city_name'], 'string', 'max'=>50],
            [['verified_by'], 'string', 'max' => 20],
            [['mobile'], 'string','max' => 11],
            [['date_verified', 'date_deleted', 'date_updated' ,'date_contracted', 'preferred_patient', 'date_terms_doctor'], 'safe'],
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    public function getHospital() {
        return $this->hasOne(Hospital::className(), ['hospital_id' => 'id']);
    }

    public function getHospitalDepartment() {
        return $this->hasOne(HospitalDepartment::className(), ['hp_dept_id' => 'id']);
    }

    public function getRegionState() {
        return $this->hasOne(RegionState::className(), ['state_id' => 'id']);
    }

    public function getRegionCity() {
        return $this->hasOne(RegionCity::className(), ['city_id' => 'id']);
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => '用户',
            'name' => '姓名',
            'mobile' => '手机号',
            'gender' => '性别',
            'hospital_id' => '所属医院',
            'hospital_name' => '所属医院',
            'hp_dept_id' => '所属科室',
            'hp_dept_name' => '所属科室',
            'clinical_title' => '临床职称',
            'academic_title' => '学术职称',
            'country_id' => '国家',
            'state_id' => '省份',
            'state_name' => '省份名称',
            'city_id' => '城市',
            'city_name' => '城市名称',
            'date_verified' => '认证日期',
            'verified_by' => '认证人员',
            'preferred_patient' => '希望收到的病人/病历',
            'date_contracted' => '签约专家签约日期',
            'date_deleted' => 'Date Deleted',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        );
    }

    public function beforeValidate() {
        if (is_null($this->country_id)) {
            $this->country_id = 1;
        }
        return parent::beforeValidate();
    }


    /**
     * 去掉不为空字段的空格
     * @return array
     */
    protected function trimAttributes() {
        return array('name', 'hospital_name', 'hp_dept_name');
    }

    /**
     * @return bool
     */
    public function isVerified() {
        return $this->date_verified !== null;
    }

    /*     * ****** Query Methods ******* */

    //医生信息查询
    public function getByUserId($userId, $attributes = null, $with = null) {
        return $this->getByAttributes(array('user_id' => $userId), $with);
    }

    public function getStateName() {
        if (strIsEmpty($this->state_name) === false) {
            return $this->state_name;
        } elseif ($this->getRegionState() !== null) {
            return $this->getRegionState()->getName();
        } else {
            return '';
        }
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getName() {
        return $this->name;
    }

    public function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getGender($text = true) {
        if ($text) {
            return StatCode::getGender($this->gender);
        } else {
            return $this->gender;
        }
    }

    public function getHospitalId() {
        return $this->hospital_id;
    }

    public function getHospitalName() {
        if (strIsEmpty($this->hospital_name) === false) {
            return $this->hospital_name;
        } elseif ($this->getHospital() !== null) {
            return $this->getHospital()->getName();
        } else {
            return '';
        }
    }

    public function getHpDeptId() {
        return $this->hp_dept_id;
    }

    public function getHpDeptName() {
        if (strIsEmpty($this->hp_dept_name) === false) {
            return $this->hp_dept_name;
        } elseif ($this->getHospitalDepartment() !== null) {
            return $this->getHospitalDepartment()->getName();
        } else {
            return '';
        }
    }

    public function getCountryId() {
        return $this->country_id;
    }

    public function getStateId() {
        return $this->state_id;
    }

    public function getCityId() {
        return $this->city_id;
    }

    public function getCityName() {
        if (strIsEmpty($this->city_name) === false) {
            return $this->city_name;
        } elseif ($this->getRegionCity() !== null) {
            return $this->getRegionCity()->getName();
        } else {
            return '';
        }
    }

    public function getDateVerified() {
        return $this->getDatetimeAttribute($this->date_verified);
    }

    public function getVerifiedBy() {
        return $this->verified_by;
    }

    public function getDateContracted() {
        return $this->date_contracted;
    }

    public function getPreferredPatient() {
        return $this->preferred_patient;
    }

    public function getClinicalTitle($text = true) {
        if ($text) {
            return StatCode::getClinicalTitle($this->clinical_title);
        } else {
            return $this->clinical_title;
        }
    }

    public function getAcademicTitle($text = true) {
        if ($text) {
            return StatCode::getAcademictitle($this->academic_title);
        } else {
            return $this->academic_title;
        }
    }

    public function setVerified() {
        $this->date_verified = new Expression("NOW()");
    }

    public function unsetVerified() {
        $this->date_verified = NULL;
    }

    public function setVerifiedBy($v) {
        $this->verified_by = $v;
    }

    public function isContractDoctor() {
        return $this->date_contracted !== null;
    }

    public function isTermsDoctor() {
        return $this->date_terms_doctor !== null;
    }

    public function isCommonweal() {
        return $this->is_commonweal !== null;
    }

}
