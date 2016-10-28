<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/10
 * Time: 17:17
 */

namespace app\modules\mobileDoctor\filter;


use app\util\Root;
use yii\base\ActionFilter;

class PatientMRCreatorContext extends ActionFilter
{
    public function beforeAction($action)
    {
        $mr_id = null;
        if (isset($_GET['mrid'])) {
            $mr_id = $_GET['mrid'];
        } elseif (isset($_POST['patientbooking']['mrid'])) {
            $mr_id = $_POST['patientbooking']['mrid'];
        }
        $user = Root::loadUser();
        //$this->loadPatientMRByIdAndCreatorId($mr_id, $user->getId());
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}