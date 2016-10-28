<?php

/**
 * This is the model class for table "surgery_doctor_join".
 *
 * The followings are the available columns in table 'surgery_doctor_join':
 * @property integer $id
 * @property integer $surgery_id
 * @property integer $doctor_id
 * @property integer $surgery_type
 * @property integer $surgery_count_min
 * @property integer $surgery_count_max
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
use yii\db\ActiveRecord;

class SurgeryDoctorJoin extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'surgery_doctor_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('surgery_id, doctor_id, surgery_type, surgery_count_min, surgery_count_max, display_order', 'numerical', 'integerOnly' => true),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, surgery_id, doctor_id, surgery_type, surgery_count_min, surgery_count_max, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'surgery_id' => 'Surgery',
            'doctor_id' => 'Doctor',
            'surgery_type' => 'Surgery Type',
            'surgery_count_min' => 'Surgery Count Min',
            'surgery_count_max' => 'Surgery Count Max',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('surgery_id', $this->surgery_id);
        $criteria->compare('doctor_id', $this->doctor_id);
        $criteria->compare('surgery_type', $this->surgery_type);
        $criteria->compare('surgery_count_min', $this->surgery_count_min);
        $criteria->compare('surgery_count_max', $this->surgery_count_max);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SurgeryDoctorJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
