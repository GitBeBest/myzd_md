<?php

class RegionController extends WebsiteController {

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('loadStates', 'loadCities'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionLoadStates() {
        $this->headerUTF8();
        $regionMgr = new RegionManager();
        $regionStates = $regionMgr->getAllStatesByCountryId(1);
        $this->renderJsonOutput($regionStates);
    }

    public function actionLoadCities($state = null) {
        $this->headerUTF8();
        $regionMgr = new RegionManager();
        $stateId = $_GET['state'];
        $models = $regionMgr->getAllCitiesByStateId($stateId);
        $this->renderJsonOutput($models);
    }
}
