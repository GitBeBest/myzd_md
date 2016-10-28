<?php

/**
 * This is the model class for table "disease".
 *
 * The followings are the available columns in table 'disease':
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $description
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
namespace app\models\disease;
use app\models\base\BaseActiveRecord;
use app\models\doctor\Doctor;
use app\models\hospital\Hospital;

/**
 * Class Disease
 * @package app\models\disease
 *
 * @property string $name
 * @property string $description
 */
class Disease extends BaseActiveRecord {

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
        return [
            [['name', 'date_created'], 'required'],
            [['category_id', 'display_order'], 'number', 'integerOnly' => true],
            [['name'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 500],
            [['date_updated', 'date_deleted'], 'safe']
        ];
    }

    public function getDoctor() {
        return $this->hasMany(Doctor::className(), ['id' => 'disease_doctor_join(disease_id, doctor_id)'])->all();
    }

    public function getExpertTeam() {
        return $this->hasMany('ExpertTeam', ['id' => 'disease_expteam_join(disease_id, expteam_id)'])->all();
    }

    public function getHospital() {
        return $this->hasMany(Hospital::className(), ['id' => 'disease_hospital_join(disease_id, hospital_id)'])->all();
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
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

}
