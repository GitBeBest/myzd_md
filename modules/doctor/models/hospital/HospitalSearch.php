<?php

class HospitalSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new NewHospital();
    }

    public function getQueryFields() {
        return array('name', 'stateid', 'statename', 'cityid', 'cityname');
    }

    public function addQueryConditions() {
        $this->criteria->addCondition('t.date_deleted is null');
        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['name'])) {
                $name = $this->queryParams['name'];
                $this->criteria->addCondition("(t.name like '%{$name}%' or t.short_name like '%{$name}%')");
            }
            if (isset($this->queryParams['stateid'])) {
                $stateid = $this->queryParams['stateid'];
                $this->criteria->compare('t.state_id', $stateid);
            }
            if (isset($this->queryParams['statename'])) {
                $statename = $this->queryParams['statename'];
                $this->criteria->compare('t.state_name', str_replace('省', '', $statename));
            }
            if (isset($this->queryParams['cityid'])) {
                $cityId = $this->queryParams['cityid'];
                $this->criteria->compare('t.city_id', $cityId);
            }

            if (isset($this->queryParams['cityname'])) {
                $cityname = $this->queryParams['cityname'];
                $this->criteria->compare('t.city_name', str_replace('市', '', $cityname));
            }
        }
    }

}
