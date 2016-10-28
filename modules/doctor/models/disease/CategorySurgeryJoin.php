<?php

/**
 * This is the model class for table "category_surgery_join".
 *
 * The followings are the available columns in table 'category_surgery_join':
 * @property integer $id
 * @property integer $sub_cat_id
 * @property integer $surgery_id
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
use yii\db\ActiveRecord;

class CategorySurgeryJoin extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'category_surgery_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('sub_cat_id, surgery_id, display_order', 'numerical', 'integerOnly' => true),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, sub_cat_id, surgery_id, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }


    public function getSurgery(){
        return $this->hasOne(Surgery::className(), ['id' => 'surgery_id']);
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'sub_cat_id' => 'Sub Cat',
            'surgery_id' => 'Surgery',
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
        $criteria->compare('sub_cat_id', $this->sub_cat_id);
        $criteria->compare('surgery_id', $this->surgery_id);
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
     * @return CategorySurgeryJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
