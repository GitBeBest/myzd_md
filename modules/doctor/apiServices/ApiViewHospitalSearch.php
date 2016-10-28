<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewBankCardInfo
 *
 * @author shuming
 */
namespace app\modules\doctor;
use \app\apiServices\EApiViewService;

class ApiViewHospitalSearch extends EApiViewService {

    private $hpSearch;
    private $list;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->list = array();
        $this->hpSearch = new HospitalSearch($searchInputs);
    }

    protected function createOutput() {
        $this->output = array(
            'status' => self::RESPONSE_OK,
            'errorCode' => 0,
            'errorMsg' => 'success',
            'results' => $this->results,
        );
    }

    protected function loadData() {
        $this->loadHospital();
    }

    public function loadHospital() {
        $models = $this->hpSearch->search();
        if (arrayNotEmpty($models)) {
            $this->setHospital($models);
        }
        $this->results->hospital = $this->list;
    }

    private function setHospital($models) {
        foreach ($models as $value) {
            $data = new stdClass();
            $data->id = $value->id;
            $data->name = $value->name;
            $this->list[] = $data;
        }
    }

}
