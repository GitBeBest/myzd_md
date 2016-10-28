<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\DiseaseManager;

class ApiViewDisease extends EApiViewService {
    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {}

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
    public function getDiseaseByName($name, $is_like){
        $disMgr = new DiseaseManager();
        $navList = $disMgr->getDiseaseByName($name, $is_like);

        return $navList;
    }
    
    public function getDiseaseByCategoryId($category_id)
    {
        $disease = new DiseaseManager();
        $navList = $disease->getDiseaseByCategoryId($category_id);

        $this->setDiseaseCategory($navList);
    }
    
    private function setDiseaseCategory($data){
        $this->results = $data;
    }

}
