<?php
namespace app\models\doctor;
use app\models\base\BaseFileModel;
use app\models\user\User;

/**
 * Class UserDoctorHuizhen
 * @package app\models\doctor
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $is_join
 * @property string $travel_duration
 * @property integer $fee_min
 * @property integer $fee_max
 * @property string $week_days
 * @property string $patients_prefer
 * @property string $freq_destination
 * @property string $destination_req
 */
class UserDoctorHuizhen extends BaseFileModel {

    const IS_JOIN = 1;  //参加
    const IS_NOT_JOIN = 0;    //不参加

    /**
     * @return string the associated database table name
     */

    public static function tableName() {
        return 'user_doctor_huizhen';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['user_id', 'is_join', 'min_no_surgery', 'fee_min', 'fee_max'], 'number', 'integerOnly' => true],
            [['week_days'], 'string', 'max' => 20],
            [['travel_duration'],'string', 'max' => 100],
            [['patients_prefer', 'freq_destination', 'destination_req'], 'string', 'max' => 500]
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
            'travel_duration' => 'Travel Duration',
            'min_no_surgery' => 'Min No Surgery',
            'fee_min' => 'Fee Min',
            'fee_max' => 'Fee Max',
            'week_days' => 'Week Days',
            'patients_prefer' => 'Patients Prefer',
            'freq_destination' => 'Freq Destination',
            'destination_req' => 'Destination Req',
        );
    }

    public function getOptionsIsJoin() {
        return array(self::IS_JOIN => '加入', self::IS_NOT_JOIN => '不参与');
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

    public function getWeekDays($v = true) {
        if ($v) {
            if (strIsEmpty($this->week_days, true) === false) {
                return explode(',', $this->week_days);
            } else {
                return array();
            }
        } else {
            return $this->week_days;
        }
    }

    public function getTravelDuration($v = true) {
        if ($v) {
            if (strIsEmpty($this->travel_duration, true) === false) {
                return explode(',', $this->travel_duration);
            } else {
                return array();
            }
        } else {
            return $this->travel_duration;
        }
    }

}
