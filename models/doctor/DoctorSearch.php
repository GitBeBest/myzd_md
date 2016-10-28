<?php
namespace app\models\doctor;
use app\models\base\BaseSearchModel;
use app\models\doctor\Doctor;

class DoctorSearch extends BaseSearchModel {

    const APP_VERSION = 8;

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.is_contracted DESC,t.role DESC,t.medical_title,convert(t.name using gbk)';
        parent::__construct($searchInputs, $with);
    }

    /**
     * return app\models\doctor\Doctor
     */
    public function model() {
        $this->model = new Doctor();
    }

    public function getQueryFields() {
        return array('name', 'city', 'state', 'disease', 'hospital', 'hpdept', 'mtitle', 'disease_name', 'disease_category', 'disease_sub_category');
    }

    public function addQueryConditions() {
        $this->searchCondition['t.is_contracted'] = 1;
        if ($this->hasQueryParams()) {
            // Doctor.Name
            if (isset($this->queryParams['name'])) {
                $this->searchCondition['t.name'] = $this->queryParams['name'];
            }
            // Doctor.medical_title
            if (isset($this->queryParams['mtitle'])) {
                $this->searchCondition['t.medical_title'] = $this->queryParams['mtitle'];
            }
            // Doctor.city
            if (isset($this->queryParams['city'])) {
                $this->searchCondition['t.city_id'] = $this->queryParams['city'];
            }
            if (isset($this->queryParams['state'])) {
                $this->searchCondition['t.state_id'] = $this->queryParams['state'];
            }
            // Disease.
            if (isset($this->queryParams['disease'])) {
                $diseaseId = $this->queryParams['disease'];
                $this->join[] = ['left join', 'disease_doctor_join ddj', 't.`id`=ddj.`doctor_id`'];
                $this->searchCondition['ddj.disease_id'] = $this->queryParams['disease'];
                $this->distinct = true;
            }
            // DiseaseName.
            if (isset($this->queryParams['disease_name'])) {
                $disease_name = $this->queryParams['disease_name'];
                $this->join[] = ['left join', 'disease_doctor_join ddj', 't.`id`=ddj.`doctor_id`'];
                $this->join[] = ['left join', 'disease d', 'd.`id` = ddj.`disease_id`'];
                $this->searchCondition["d.app_version"] =  self::APP_VERSION;
                $this->searchCondition['d.name'] = $disease_name;
                $this->distinct = true;
            }
            if (isset($this->queryParams['hospital'])) {
                $this->searchCondition["t.hospital_id"] =  $this->queryParams['hospital'];
            }
            if (isset($this->queryParams['hpdept'])) {
                $this->join[] = ['left join', 'hospital_dept_doctor_join hddj', 't.`id`=hddj.`doctor_id`'];
                $this->searchCondition['hddj.hp_dept_id'] =  $this->queryParams['hpdept'];
                $this->distinct = true;
            }
            // disease_category.
            if (isset($this->queryParams['disease_category'])) {
                $this->join[] = ['left join', 'disease_doctor_join b', 't.id=b.doctor_id'];
                $this->join[] = ['left join', 'category_disease_join c', 'c.disease_id=b.disease_id'];
                $this->join[] = ['left join', 'disease_category d', 'd.sub_cat_id=c.sub_cat_id'];
                $this->searchCondition['d.cat_id'] = $this->queryParams['disease_category'];
                $this->searchCondition['d.app_version'] = self::APP_VERSION;
                $this->distinct = true;
            }
            // disease_sub_category.
            if (isset($this->queryParams['disease_sub_category'])) {
                $sub_cat_id = $this->queryParams['disease_sub_category'];
                $this->join[] = ['left join', 'disease_doctor_join b', 't.id=b.doctor_id'];
                $this->join[] = ['left join', 'category_disease_join c', 'c.disease_id=b.disease_id'];
                $this->searchCondition['c.sub_cat_id'] = $sub_cat_id;
                $this->distinct = true;
                //医生专业
                $this->searchCondition['t.sub_cat_id'] = $sub_cat_id;
            }
        }
    }

}
