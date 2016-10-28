<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/10
 * Time: 17:16
 */

namespace app\modules\mobileDoctor\filter;


use yii\base\ActionFilter;

class PatientContext extends ActionFilter
{
    public function beforeAction($action)
    {
        $patientId = null;
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
        } else if (isset($_POST['patient']['id'])) {
            $patientId = $_POST['patient']['id'];
        }

        //$this->loadPatientInfoById($patientId);
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}