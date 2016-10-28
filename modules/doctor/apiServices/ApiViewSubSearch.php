<?php

class ApiViewSubSearch extends EApiViewService {

    private $id;
    private $name;
    private $list;
    private $manager;

    public function __construct($id, $name) {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->manager = new Manager();
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
        $this->loadSearch();
    }

    private function loadSearch() {
        $models = $this->manager->searchSubcat($this->id, $this->name);
        if (arrayNotEmpty($models)) {
            $this->setSearch($models);
        }
        $this->results->diseaseList = $this->list;
    }

    private function setSearch($models) {
        foreach ($models as $v) {
            $dis = new stdClass();
            $dis->diseaseId = $v->id;
            $dis->diseaseName = $v->name;
            $this->list[] = $dis;
        }
    }

}
