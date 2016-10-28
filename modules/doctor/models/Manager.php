<?php

class Manager {

    public function saveBasic($values) {
        $output = array('status' => 'no');
        $form = new BasicInfoForm();
        $form->setAttributes($values, true);
        if ($form->validate()) {
            if (strIsEmpty($form->id)) {
                $model = new NewDoctor();
                $attributes = $form->getSafeAttributes();
                $model->setAttributes($attributes, true);
                if ($model->save()) {
                    $output['status'] = 'ok';
                    $output['id'] = $model->getId();
                } else {
                    $output['errors'] = $model->getErrors();
                }
            } else {
                $model = NewDoctor::model()->getById($form->id);
                $attributes = $form->getSafeAttributes();
                $model->setAttributes($attributes, true);
                if ($model->update(array('name', 'gender', 'birthday', 'mobile', 'email', 'remote_domain', 'remote_file_key'))) {
                    $output['status'] = 'ok';
                    $output['id'] = $model->getId();
                } else {
                    $output['errors'] = $model->getErrors();
                }
            }
        } else {
            $output['errors'] = $form->getErrors();
        }
        return $output;
    }

    public function saveMajor($values) {
        $output = array("status" => 'no');
        if (arrayNotEmpty($values['diseaseList']) && arrayNotEmpty($values['surgeryList'])) {
            $doctorId = $values['id'];
            $dbTran = Yii::app()->db->beginTransaction();
            try {
                $diseaseList = $values['diseaseList'];
                foreach ($diseaseList as $v) {
                    $diseaseDoctorJoin = new DiseaseDoctorJoin();
                    $diseaseDoctorJoin->disease_id = $v;
                    $diseaseDoctorJoin->doctor_id = $doctorId;
                    $diseaseDoctorJoin->save();
                }
                $surgeryList = $values['surgeryList'];
                foreach ($surgeryList as $values) {
                    $surjoin = new SurgeryDoctorJoin();
                    $surjoin->doctor_id = $doctorId;
                    $surjoin->surgery_id = $values['id'];
                    $surjoin->surgery_type = $values['type'];
                    $surjoin->surgery_count_min = $values['min'];
                    $surjoin->surgery_count_max = $values['max'];
                    $surjoin->save();
                }
                $dbTran->commit();
                $output['status'] = 'ok';
                $output['id'] = $doctorId;
            } catch (CDbException $e) {
                $output['errors'] = $e->getMessage();
                $dbTran->rollback();
              
            } catch (CException $e) {
                $output['errors'] = '网络连接异常';
                $dbTran->rollback();
            }
        } else {
            $output['errors'] = 'miss data..';
        }
        
        return $output;
    }

    public function searchSubcat($id, $name) {
        $doctor = NewDoctor::model()->getById($id);
        $criteria = new CDbCriteria;
        $criteria->with = array('diseasejoin');
        $criteria->addSearchCondition('t.name', $name);
        $criteria->compare('diseasejoin.sub_cat_id', $doctor->category_id);
        $criteria->compare('t.app_version', 8);
        return Disease::model()->findAll($criteria);
    }

    public function searchSurgery($id, $name) {
        $doctor = NewDoctor::model()->getById($id);
        $criteria = new CDbCriteria;
        $criteria->with = array('surgeryjoin');
        $criteria->addSearchCondition('t.name', $name);
        $criteria->compare('surgeryjoin.sub_cat_id', $doctor->category_id);
        return Surgery::model()->findAll($criteria);
    }

    public function getSubIdForList($list) {
        $output = array();
        foreach ($list as $value) {
            $output[] = $value->sub_cat_id;
        }
        return $output;
    }

}
