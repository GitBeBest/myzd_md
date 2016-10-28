<?php
namespace app\models\doctor;
use app\models\base\BaseActiveRecord;
use app\util\StatCode;

/**
 * Class Doctor
 * @package app\models\doctor
 *
 * @property integer $id
 * @property string  $name
 * @property string  $fullname
 * @property string  $mobile
 * @property integer $hospital_id
 * @property integer $hp_dept_id
 * @property string  $faculty
 * @property integer $state_id
 * @property integer $city_id
 * @property string  $medical_title
 * @property string  $academic_title
 * @property integer $gender
 * @property string $disease_specialty
 * @property string $surgery_specialty
 * @property string $search_keywords
 * @property string $description
 * @property boolean $is_contracted
 * @property integer $role
 * @property string $honour
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $password_raw
 * @property string $wechat
 * @property boolean $career_exp
 * @property string $tel
 * @property integer $display_order
 * @property string $hospital_name
 * @property string $hp_dept_name
 * @property string $date_activated
 * @property string $date_verified
 * @property string $last_login_time
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class Doctor extends BaseActiveRecord {

//    const GENDER_MALE = 1;
//    const GENDER_FEMALE = 2;
    const M_TITLE_ZHUREN = 1;
    const M_TITLE_ZHUREN_ASSOC = 2;
    const M_TITLE_ZHUZHI = 3;
    const M_TITLE_ZHUYUANYISHI = 4;
    const A_TITLE_PROF = 1;
    const A_TITLE_PROF_ASSOC = 2;
    const A_TITLE_NONE = 9;
    const ROLE_DOCTOR = 1;

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'doctor';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['name', 'fullname', 'medical_title'], 'required'],
            [['hospital_id', 'hp_dept_id', 'gender', 'role', 'display_order', 'state_id', 'city_id'], 'number', 'integerOnly' => true],
            [['name', 'fullname', 'hospital_name', 'hp_dept_name', 'faculty', 'medical_title', 'academic_title', 'password_raw', 'wechat', 'tel'], 'string', 'max' => 45],
            [['mobile'], 'string', 'max' => 11],
            [['disease_specialty', 'surgery_specialty','specialty', 'avatar_url'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 500],
            [['email', 'search_keywords'], 'string', 'max' => 100],
            [['password'], 'max' => 64],
            [['salt'], 'max' => 40],
            [['date_activated', 'date_verified', 'last_login_time', 'date_created', 'date_updated', 'date_deleted'], 'safe']
        ];
    }

    public function getDoctorAvatar()
    {
        return $this->hasOne('DoctorAvatar', ['id' => 'doctor_id']);
    }

    public function getDoctorCerts()
    {
        return $this->hasMany('DoctorCert', ['doctor_id' => 'id']);
    }

    public function getDoctorExpertTeam()
    {
        return $this->hasOne('ExpertTeam', ['id' => 'leader_id']);
    }

    /**
     * belongs_to
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorHospital(){
        return $this->hasOne('Hospital', ['hospital_id' => 'id']);
    }

    /**
     * belongs_to
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorHpDept() {
        return $this->hasOne('HospitalDepartment', ['hp_dept_id' => 'id']);
    }

    /**
     * belongs_to
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorState() {
        return $this->hasOne('RegionState', ['state_id' => 'id']);
    }

    /**
     * belongs_to
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorCity() {
        return $this->hasOne('RegionCity', ['city_id' => 'id']);
    }

    /**
     * many to many
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorFaculties() {
        return $this->hasMany('Faculty', ['id' => 'faculty_doctor_join(faculty_id, doctor_id)'])->all();
    }

    /**
     * many to many
     * @return \yii\db\ActiveQuery
     */
    public function getDoctorDiseases() {
        return $this->hasMany('Disease', ['id' => 'disease_doctor_join(disease_id, doctor_id)'])->all();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '姓名（展示）',
            'fullname' => '姓名',
            'mobile' => '手机',
            'is_contracted' => '是否签约',
            'state_id' => "省份",
            'city_id' => "城市",
            'hospital_id' => '所属医院',
            'hospital_name' => '所属医院',
            'hp_dept_id' => '所属科室',
            'hp_dept_name' => '所属科室',
            'faculty' =>  '科室',
            'title' =>  '职称',
            'medical_title' =>  '临床职称',
            'academic_title' => '学术职称',
            'gender' => '性别',
            'disease_specialty' => '擅长疾病',
            'surgery_specialty' => '擅长手术',
            'specialty' => '关联疾病',
            'search_keywords' => '搜索关键词',
            'career_exp' =>  '执业经历',
            'description' => '擅长描述',
            'role' => '角色',
            'honour' => '荣誉',
            'email' => '邮箱',
            'password' => "登录密码",
            'salt' => 'Salt',
            'password_raw' => 'Password Raw',
            'wechat' => '微信',
            'tel' => '电话（座机）',
            'display_order' => 'Display Order',
            'date_activated' => 'Date Activated',
            'date_verified' => 'Date Verified',
            'last_login_time' => 'Last Login Time',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    //去掉不为空字段的空格
    protected function trimAttributes() {
        return array('name', 'fullname', 'description');
    }

    public function afterFind() {
        // convert json string to array.
        if (!is_null($this->honour)) {
            $this->honour = explode('#', $this->honour);
        }
        return parent::afterFind();
    }

    /*     * ****** Public Methods ******* */

    public function prepareNewModel() {
        $this->_createSalt();
        $this->_createPassword();
    }

    /*     * ****** Private Methods ******* */

    private function _createSalt() {
        $this->salt = $this->_strRandom(40);
    }

    private function _createPassword() {
        $this->setPassword($this->_encryptPassword($this->password_raw));
    }

    public function _encryptPassword($password, $salt = null) {
        if ($salt === null) {
            return ($this->_encrypt($password . $this->salt));
        } else {
            return ($this->_encrypt($password . $salt));
        }
    }

    private function _encrypt($value) {
        return hash('sha256', $value);
    }

    // Max length supported is 62.
    private function _strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));

        return ($ret);
    }

    public function getByDoctorId($doctor_id) {
        return self::find()->where(['doctor_id' => $doctor_id, 'date_deleted' => null])->one();
    }

    public function getByDiseaseId($diseaseId, $doctor_id) {
        return self::find()
            ->leftJoin('disease_doctor_join as b','b.`doctor_id` = t.`id`')
            ->where(['in','doctor_id', [$doctor_id]])
            ->andWhere(['b.disease_id' => $diseaseId])
            ->andWhere(['date_deleted' => null])
            ->limit(3)
            ->all();
    }

    /*     * ****** Display Methods ******* */

    public function getAbsUrlAvatar($thumbnail = false) {
        if ($this->has_remote == 1) {
            return $this->remote_domain . $this->remote_file_key;
        }
        if (isset($this->avatar_url) && $this->avatar_url != '') {
            $url = $this->avatar_url;
            if (strStartsWith($url, 'http')) {
                // $url is already an absolute internet url.
                return $url;
            } else {
                // append 'http://domain.com' to the head of $url.
                return $this->getRootUrl() . $url;
            }
        } else {
            //default doctor avatar image.
            return 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/d/doctor_default155x155.jpg';
        }
    }

    public function getRootUrl() {
        if (isset($this->base_url) && ($this->base_url != '')) {
            return $this->base_url;
        } else {
            return \Yii::$app->request->baseUrl . '/';
        }
    }

    public function getOptionsMedicalTitle() {
        return array(
            self::M_TITLE_ZHUREN => '主任医师',
            self::M_TITLE_ZHUREN_ASSOC => '副主任医师',
            self:: M_TITLE_ZHUZHI => '主治医师',
            self:: M_TITLE_ZHUYUANYISHI => '住院医师'
        );
    }

    /**
     * @NOTE do not use this method.
     * @return string
     */
    public function getTitle() {
        return $this->getMedicalTitle();
    }

    public function getMedicalTitle() {
        $options = $this->getOptionsMedicalTitle();
        if (isset($options[$this->medical_title]))
            return $options[$this->medical_title];
        else
            return '';
    }

    public function getOptionsAcademicTitle() {
        return array(
            self::A_TITLE_PROF => '教授',
            self::A_TITLE_PROF_ASSOC => '副教授',
            self::A_TITLE_NONE => '无'
        );
    }

    public function getAcademicTitle() {
        $options = $this->getOptionsAcademicTitle();
        if (isset($options[$this->academic_title]))
            return $options[$this->academic_title];
        else
            return '';
    }

    public function getOptionsGender() {
        return array(
            StatCode::GENDER_MALE => '男',
            StatCode::GENDER_FEMALE => '女'
        );
    }

    public function getGender($text = true) {
        if ($text) {
            $options = $this->getOptionsGender();
            if (isset($options[$this->gender]))
                return $options[$this->gender];
            else
                return '';
        }else {
            return $this->gender;
        }
    }

    public function getFileUploadRootPath() {
        return \Yii::$app->params['doctorAvatar'];
    }

    /**
     * gets the file upload path of given foler name.
     * @param string $folderName
     * @return string
     */
    public function getFileUploadPath($folderName = null) {
        if ($folderName === null) {
            return $this->getFileUploadRootPath();
        } else {
            return ($this->getFileUploadRootPath() . $folderName);
        }
    }

    public function getReasons() {
        $data = array();
        isset($this->reason_one) && $data[] = $this->reason_one;
        isset($this->reason_two) && $data[] = $this->reason_two;
        isset($this->reason_three) && $data[] = $this->reason_three;
        isset($this->reason_four) && $data[] = $this->reason_four;
        return $data;
    }

    public function getName() {
        return $this->name;
    }
}
