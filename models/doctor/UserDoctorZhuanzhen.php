<?php
namespace app\models\doctor;
use app\models\base\BaseFileModel;
use app\models\user\User;

/**
 * Class UserDoctorZhuanzhen
 * @package app\models\doctor
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $is_join
 * @property integer $fee
 * @property string $week_days
 * @property string $prep_days
 */
class UserDoctorZhuanzhen extends BaseFileModel {

    const IS_JOIN = 1;  //参加
    const IS_NOT_JOIN = 0;    //不参加
    const BED_IN_DAYS_THREE = '3d'; //三天内安排床位
    const BED_IN_ONE_WEEK = '1w'; //一周内安排床位
    const BED_IN_TWO_WEEKS = '2w'; //两周内安排床位
    const BED_IN_THREE_WEEKS = '3w'; //三周内安排床位

    /**
     * @return string the associated database table name
     */

    public static function tableName() {
        return 'user_doctor_zhuanzhen';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['user_id', 'is_join', 'fee'], 'number', 'integerOnly' => true],
            [['prep_days', 'preferred_patient'], 'string', 'max' => 500],
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'is_join' => 'Is Join',
            'fee' => 'Fee',
            'prep_days' => 'Prep Days',
        );
    }

    public function getOptionsIsJoin() {
        return array(self::IS_JOIN => '加入', self::IS_NOT_JOIN => '不参与');
    }

    public function getOptionsPrepDays() {
        return array(
            self::BED_IN_DAYS_THREE => '三天内',
            self::BED_IN_ONE_WEEK => '一周内',
            self::BED_IN_TWO_WEEKS => '两周内',
            self::BED_IN_THREE_WEEKS => '三周内'
        );
    }

    public function getIsJoin($v = true) {
        if ($v) {
            $options = $this->getOptionsIsJoin();
            if (isset($options[$this->is_join])) {
                return $options[$this->is_join];
            } else {
                return '';
            }
        }
        return $this->is_join;
    }

    public function getPrepDays($v = true) {
        if ($v) {
            $options = $this->getOptionsPrepDays();
            if (isset($options[$this->prep_days])) {
                return $options[$this->prep_days];
            } else {
                return '';
            }
        }
        return $this->prep_days;
    }

}
