<?php

/**
 * This is the model class for table "new_category_disease_join".
 *
 * The followings are the available columns in table 'new_category_disease_join':
 * @property integer $id
 * @property integer $sub_cat_id
 * @property integer $disease_id
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
use yii\db\ActiveRecord;

class CategoryDiseaseJoin extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'category_disease_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('sub_cat_id, disease_id, display_order', 'numerical', 'integerOnly' => true),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, sub_cat_id, disease_id, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    public function getDisease()
    {
        return $this->hasOne(Disease::className(), ['id' => 'disease_id'])->where('disease.app_version = 8');
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'sub_cat_id' => 'Sub Cat',
            'disease_id' => 'Disease',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }
}
