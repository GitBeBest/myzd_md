<?php

class ApiViewSurgerySearch extends EApiViewService {

    private $id;
    private $name;
    private $list;

    public function __construct($id, $name) {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
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
        $manager = new Manager();
        $models = $manager->searchSurgery($this->id, $this->name);
        if (arrayNotEmpty($models)) {
            $this->setSearch($models);
        }
        $this->results->surgeryList = $this->list;
    }

    private function setSearch($models) {
        foreach ($models as $v) {
            $sur = new stdClass();
            $sur->surgeryId = $v->id;
            $sur->surgeryName = $v->name;
            $this->list[] = $sur;
        }
    }

}
