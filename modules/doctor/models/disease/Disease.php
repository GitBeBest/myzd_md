<?php

/**
 * This is the model class for table "new_disease".
 *
 * The followings are the available columns in table 'new_disease':
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $description
 * @property integer $level
 * @property string $basic_price
 * @property string $app_version
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
use yii\db\ActiveRecord;

class Disease extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'disease';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, date_created', 'required'),
            array('category_id, level, display_order', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 20),
            array('description', 'length', 'max' => 500),
            array('basic_price, app_version', 'length', 'max' => 10),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, category_id, description, level, basic_price, app_version, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }


    public function getDiseaseJoin(){
        return $this->hasMany(CategoryDiseaseJoin::className(),['id' => 'disease_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'category_id' => 'Category',
            'description' => 'Description',
            'level' => 'Level',
            'basic_price' => 'Basic Price',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('level', $this->level);
        $criteria->compare('basic_price', $this->basic_price, true);
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
     * @return NewDisease the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
