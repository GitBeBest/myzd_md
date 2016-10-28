<?php
namespace app\models\doctor;
use app\models\base\BaseFormModel;
use app\models\region\RegionCity;
use app\models\region\RegionState;
use yii\helpers\ArrayHelper;

class DoctorBankCardForm extends BaseFormModel {

    public $id;
    public $user_id;
    public $name;
    public $card_no;
    public $state_id;
    public $city_id;
    public $bank;
    public $subbranch;
    public $is_default;
    public $options_state;
    public $options_city;

    public function rules() {
        return [
            [['user_id', 'state_id', 'city_id', 'is_default'], 'number', 'integerOnly' => true],
            [['name', 'card_no', 'bank', 'subbranch'], 'string', 'max' => 50],
            [['id', 'card_no'], 'safe']
        ];
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '持卡人',
            'card_no' => '卡号',
            'state_id' => 'State',
            'state_name' => 'State Name',
            'city_id' => 'City',
            'city_name' => 'City Name',
            'bank' => '开户银行',
            'subbranch' => '银行支行',
            'is_default' => '默认'
        );
    }

    public function initModel(DoctorBankCard $model = null) {
        if (isset($model)) {
            $attributes = $model->getAttributes();
            $this->setAttributes($attributes, true);
        }
    }

    public function loadOptionsState() {
        if (is_null($this->options_state)) {
            $this->options_state = ArrayHelper::map((new RegionState())->getAllByCountryId(1), 'id', 'name');
        }
        return $this->options_state;
    }

    public function loadOptionsCity() {
        if (is_null($this->state_id)) {
            $this->options_city = array();
        } else {
            $this->options_city = ArrayHelper::map((new RegionCity())->getAllByStateId($this->state_id), 'id', 'name');
        }
        return $this->options_city;
    }

}
