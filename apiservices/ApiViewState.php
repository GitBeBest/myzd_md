<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\doctor\Doctor;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ApiViewState extends EApiViewService {

    private $country_id = 1;

    public function __construct() {
        parent::__construct();
        $this->results = new \stdClass();
    }

    protected function loadData() {
        $this->loadState();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    public function loadState() {
        $result = Doctor::find()->alias('t')
            ->select(['s.id','s.name'])
            ->join('LEFT JOIN', 'hospital h', 'h.id = t.hospital_id')
            ->join('LEFT JOIN', 'region_state s', 'h.state_id = s.id')
            ->where(['t.is_contracted' => 1, 't.`date_deleted`' => null])
            ->groupBy('s.id')
            ->having('s.id IS NOT NULL')
            ->all();
        $states = ArrayHelper::map($result, 'id', 'name');
        $this->results->stateList = $states;
    }

}
