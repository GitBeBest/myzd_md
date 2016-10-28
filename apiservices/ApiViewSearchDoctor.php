<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\doctor\Doctor;
use app\models\doctor\DoctorSearchForm;

class ApiViewSearchDoctor extends EApiViewService 
{
    private $name;
    private $is_like;
    private $status = false;
    private $errorMsg = false;

    public function __construct($name, $is_like = 0)
    {
        parent::__construct();
        $this->name = $name;
        $this->is_like = $is_like;
    }
    
    protected function loadData() 
    {
        $this->SearchDoctor();
    }
    
    protected function createOutput() 
    {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => $this->status === false ? self::RESPONSE_OK : $this->status,
                'errorCode' => 0,
                "errorMsg" => $this->errorMsg === false ? 'success' : $this->errorMsg,
                'results' => $this->results,
            );
        }
    }

    protected function SearchDoctor() 
    {
        $form = new DoctorSearchForm();
        $form->setAttributes(array('name' => $this->name), true);
        if ($form->validate()) {
            $data = $form->getSafeAttributes();
            $doctorInfo = $this->is_like == 1 ?
                (new Doctor())->find()->where(['is_contracted' => 0])->andWhere(['like', 'name', $data['name']])->all():
                (new Doctor())->find()->where(['is_contracted' => 1, 'name' => $data['name']])->all();
            $result = array();
            /**
             * @var $r Doctor
             */
            foreach ($doctorInfo as $r) {
                $data = new \stdClass();
                $data->id = $r->getId();
                $data->name = $r->name;
                $data->hpName = $r->hospital_name;
                $data->mTitle = $r->getMedicalTitle();
                $data->aTitle = $r->getAcademicTitle();
                $data->imageUrl = $r->getAbsUrlAvatar();
                $data->hpDeptName = $r->hp_dept_name;
                $data->isContracted = $r->is_contracted;
                $data->desc = $r->description;
                $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/contractdoctor/' . $r->getId());
                $result[] = $data;
            }

            $this->results = $result;

        } else {
            $this->status = self::RESPONSE_VALIDATION_ERRORS;
            $this->errorMsg = $form->getFirstErrors();
        }
    }
}
