<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\patient\PatientInfo;
use app\models\PatientManager;

/**
 * Description of ApiViewPatientSearch
 *
 * @author shuming
 */
class ApiViewPatientSearch extends EApiViewService {

    private $creatorId;
    private $patientMgr;
    private $patients;
    private $name;
    private $api = false;

    //初始化类的时候将参数注入
    public function __construct($creatorId, $name, $api) {
        parent::__construct();
        $this->api = $api;
        $this->name = $name;
        $this->creatorId = $creatorId;
        $this->patientMgr = new PatientManager();
        $this->patients = null;
    }

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

    protected function loadData() {
        $this->loadPatients();
    }

    private function loadPatients() {
        $model = new PatientInfo();
        $result = $model->find()->alias('t')->with('patientBookings')
            ->where(['t.creator_id' => $this->creatorId, 't.date_deleted' => null])
            ->andWhere(['like','t.name',$this->name])
            ->all();

        if(arrayNotEmpty($result)) {
            $this->setPatientList($result);
        }

        $this->results->patientList = $result;
    }

    /**
     * 查询到的数据过滤
     * @param array $models
     */
    private function setPatientList(array $models) {
        /**
         * @var $model PatientInfo
         */
        foreach ($models as $model) {
            if($this->api == 3) {
                if(arrayNotEmpty($model->patientBookings) === false) {
                    $data = new \stdClass();
                    $data->id = $model->getId();
                    $data->name = $model->getName();
                    $data->age = $model->getAge();
                    $data->ageMonth = $model->getAgeMonth();
                    $data->cityName = $model->getCityName();
                    $data->gender = $model->getGender();
                    $data->mobile = $model->getMobile();
                    $data->diseaseName = $model->getDiseaseName();
                    $data->dateUpdated = $model->getDateUpdated('Y-m-d H:i:s');
                    $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/patientinfo/' . $model->getId());
                    $this->patients[] = $data;
                }
            }
            else {
                $data = new \stdClass();
                $data->id = $model->getId();
                $data->name = $model->getName();
                $data->age = $model->getAge();
                $data->ageMonth = $model->getAgeMonth();
                $data->cityName = $model->getCityName();
                $data->gender = $model->getGender();
                $data->mobile = $model->getMobile();
                $data->diseaseName = $model->getDiseaseName();
                $data->dateUpdated = $model->getDateUpdated('Y-m-d H:i:s');
                $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/patientinfo/' . $model->getId());
                $this->patients[] = $data;
            }
        } 
    }

}
