<?php

/**
 * This is the model class for table "new_disease_category".
 *
 * The followings are the available columns in table 'new_disease_category':
 * @property integer $id
 * @property integer $cat_id
 * @property string $cat_name
 * @property integer $sub_cat_id
 * @property string $sub_cat_name
 * @property string $description
 * @property integer $expteam_id
 * @property string $app_version
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
use yii\db\ActiveRecord;

class DiseaseCategory extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'disease_category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('cat_id, sub_cat_id, expteam_id, display_order', 'numerical', 'integerOnly' => true),
            array('cat_name, sub_cat_name', 'length', 'max' => 50),
            array('description', 'length', 'max' => 100),
            array('app_version', 'length', 'max' => 10),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, cat_id, cat_name, sub_cat_id, sub_cat_name, description, expteam_id, app_version, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'disease' => array(self::HAS_MANY, 'disease', '', 'on' => 't.sub_cat_id = disease.category_id and disease.app_version=8'),
            'surgery' => array(self::HAS_MANY, 'surgery', '', 'on' => 't.sub_cat_id = surgery.category_id'),
            'surgeryJoin' => array(self::HAS_MANY, 'CategorySurgeryJoin', '', 'on' => 't.sub_cat_id = surgeryJoin.sub_cat_id'),
            'diseaseJoin' => array(self::HAS_MANY, 'CategoryDiseaseJoin', '', 'on' => 't.sub_cat_id = diseaseJoin.sub_cat_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'cat_id' => 'Cat',
            'cat_name' => 'Cat Name',
            'sub_cat_id' => 'Sub Cat',
            'sub_cat_name' => 'Sub Cat Name',
            'description' => 'Description',
            'expteam_id' => 'Expteam',
            'app_version' => 'App Version',
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
        $criteria->compare('cat_id', $this->cat_id);
        $criteria->compare('cat_name', $this->cat_name, true);
        $criteria->compare('sub_cat_id', $this->sub_cat_id);
        $criteria->compare('sub_cat_name', $this->sub_cat_name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('expteam_id', $this->expteam_id);
        $criteria->compare('app_version', $this->app_version, true);
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
     * @return NewDiseaseCategory the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadAllCatSub() {
        $criteria = new CDbCriteria();
        $criteria->select = 't.sub_cat_id,t.sub_cat_name';
        $criteria->distinct = true;
        $criteria->addCondition("t.app_version =8");
        $criteria->addCondition("t.date_deleted is null");
        return $this->findAll($criteria);
    }

    public function loadAllSubCatByCatId($catId, $with = null) {
        return $this->getAllByAttributes(array('t.sub_cat_id' => $catId, 't.app_version' => 8), $with);
    }

}
