<?php
namespace app\models;

use app\models\base\ErrorList;
use app\models\patient\PatientBooking;
use app\models\patient\PatientDiseaseForm;
use app\models\patient\PatientInfo;

class PatientManager {

    public function loadPatientInfoById($id) {
        return (new PatientInfo())->getById($id);
    }

    /**
     * 患者详情查询
     * @param $id
     * @param $creatorId
     * @param $attributes
     * @param null $with
     * @param null $options
     * @return PatientInfo
     */
    public function loadPatientInfoByIdAndCreatorId($id, $creatorId, $attributes, $with = null, $options = null) {
        if (is_null($attributes)) {
            $attributes = '*';
        }
        return (new PatientInfo())->getByIdAndCreatorId($id, $creatorId, $attributes, $with, $options);
    }

    /**
     * 查询患者列表
     * @param $creatorId
     * @param $attributes
     * @param null $with
     * @param null $options
     * @return mixed
     */
    public function loadPatientInfoListByCreatorId($creatorId, $attributes, $with = null, $options = null) {
        if (is_null($attributes)) {
            $attributes = '*';
        }
        return (new PatientInfo())->getAllByCreatorId($creatorId, $attributes, $with, $options);
    }

    /**
     * 查询所有患者信息总数
     * @param $creator_id
     * @return int
     */
    public function loadPatientCount($creator_id) {
        return count((new PatientInfo())->findAll(['creator_id' => $creator_id, 'date_deleted'=> null]));
    }

    /**
     * 查询创建者预约列表
     * @param $creatorId
     * @param $status
     * @param null $attributes
     * @param null $with
     * @param null $options
     * @return mixed
     */
    public function loadAllPatientBookingByCreatorId($creatorId, $status, $attributes = null, $with = null, $options = null) {
        if (is_null($attributes)) {
            $attributes = '*';
        }
        return (new PatientBooking())->getAllByCreatorId($creatorId, $status, $attributes, $with, $options);
    }

    public function loadAllPatientBookingByCreatorIdAndName($creatorId, $name, $with) {
        $model = new PatientBooking();
        $result = $model->find()->alias('t')
            ->joinWith($with)
            ->where(['t.creator_id' => $creatorId])
            ->andWhere(['like', 't.patient_name', $name])
            ->orWhere(['like', 't.expected_doctor', $name])
            ->orderBy('t.date_updated desc')
            ->all();

        return $result;
    }

    //查询预约该医生的患者列表
    public function loadPatientBookingListByDoctorId($doctorId, $attributes = '*', $with = null, $options = null) {
        return (new PatientBooking())->getAllByDoctorId($doctorId, $with = null, $options = null);
    }

    public function apiSaveDiseaseByPatient($values) {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $form = new PatientDiseaseForm();
        $form->setAttributes($values, true);
        if ($form->validate()) {
            $data = $form->getSafeAttributes();
            $attr = array('id' => $data['id']);
            unset($data['id']);
            $return = \Yii::$app->db->createCommand()->update(PatientInfo::tableName(), $data, 'id = ' . (int)$values['id'])->execute();
            if($return != 0) {
                $output['status'] = 'ok';
                $output['errorCode'] = 0;
                $output['errorMsg'] = 'success';
            } else {
                $output['errorMsg'] = 'failed';
            }
        } else {
            $output['errorMsg'] = 'validate error';
        }

        return $output;
    }
}
