<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewSurgery
 *
 * @author ShuMing
 */
class ApiViewSurgery extends EApiViewService {

    private $catid;
    private $list;

    public function __construct($catid) {
        parent::__construct();
        $this->catid = $catid;
        $this->list = array();
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
        $this->loadSurgerys();
    }

    private function loadSurgerys() {
        $with = array('surgeryJoin' => array('surgery'));
        $models = DiseaseCategory::model()->loadAllSubCatByCatId($this->catid, $with);
        if (arrayNotEmpty($models)) {
            $this->setSurgerys($models);
        }
        $this->results->subcatList = $this->list;
    }

    private function setSurgerys($models) {
        foreach ($models as $value) {
            $data = new stdClass();
            $data->subCatId = $value->sub_cat_id;
            $data->subCatName = $value->sub_cat_name;
            $surgery = array();
            if (arrayNotEmpty($value->surgeryJoin)) {
                foreach ($value->surgeryJoin as $v) {
                    if (isset($v->surgery)) {
                        $s = $v->surgery;
                        $dis = new stdClass();
                        $dis->surgeryId = $s->id;
                        $dis->surgeryName = $s->name;
                        $dis->isCommon = $v->is_common;
                        $surgery[] = $dis;
                    }
                }
            }
            $data->surgeryList = $surgery;
            $this->list[] = $data;
        }
    }

}
