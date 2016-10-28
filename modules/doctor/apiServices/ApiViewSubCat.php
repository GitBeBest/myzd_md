<?php

class ApiViewSubCat extends EApiViewService {

    private $catid;
    private $subcats;

    public function __construct($catid) {
        parent::__construct();
        $this->catid = $catid;
        $this->subcats = array();
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
        $this->loadSubcats();
    }

    private function loadSubcats() {
        $models = DiseaseCategory::model()->loadAllSubCatByCatId($this->catid, array('diseaseJoin' => array('disease')));
        if (arrayNotEmpty($models)) {
            $this->setSubcat($models);
        }
        $this->results->subcatList = $this->subcats;
    }

    private function setSubcat($models) {
        foreach ($models as $value) {
            $data = new stdClass();
            $data->subCatId = $value->sub_cat_id;
            $data->subCatName = $value->sub_cat_name;
            $diseases = array();
            if (arrayNotEmpty($value->diseaseJoin)) {
                foreach ($value->diseaseJoin as $v) {
                    if (isset($v->disease)) {
                        $d = $v->disease;
                        $dis = new stdClass();
                        $dis->diseaseId = $d->id;
                        $dis->diseaseName = $d->name;
                        $dis->isCommon = $v->is_common;
                        $diseases[] = $dis;
                    }
                }
            }
            $data->diseaseList = $diseases;
            $this->subcats[] = $data;
        }
    }

}
