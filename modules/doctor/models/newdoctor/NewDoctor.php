<?php

/**
 * This is the model class for table "new_doctor".
 *
 * The followings are the available columns in table 'new_doctor':
 * @property integer $id
 * @property string $name
 * @property integer $gender
 * @property string $birthday
 * @property string $mobile
 * @property string $email
 * @property integer $hospital_id
 * @property string $hospital_name
 * @property integer $category_id
 * @property string $cat_name
 * @property integer $clinic_title
 * @property integer $academic_title
 * @property string $remote_domain
 * @property string $remote_file_key
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class NewDoctor extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'new_doctor';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('gender, hospital_id, category_id, clinic_title, academic_title, display_order', 'numerical', 'integerOnly' => true),
            array('name, email, hospital_name, cat_name', 'length', 'max' => 50),
            array('mobile', 'length', 'max' => 20),
            array('remote_domain', 'length', 'max' => 225),
            array('remote_file_key', 'length', 'max' => 100),
            array('birthday, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, gender, birthday, mobile, email, hospital_id, hospital_name, category_id, cat_name, clinic_title, academic_title, remote_domain, remote_file_key, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'diseaseCategory' => array(self::HAS_MANY, 'CategoryDiseaseJoin', '', 'on' => 't.category_id = diseaseCategory.sub_cat_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'hospital_id' => 'Hospital',
            'hospital_name' => 'Hospital Name',
            'category_id' => 'Category',
            'cat_name' => 'Cat Name',
            'clinic_title' => 'Clinic Title',
            'academic_title' => 'Academic Title',
            'remote_domain' => 'Remote Domain',
            'remote_file_key' => 'Remote File Key',
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
        $criteria->compare('gender', $this->gender);
        $criteria->compare('birthday', $this->birthday, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('hospital_id', $this->hospital_id);
        $criteria->compare('hospital_name', $this->hospital_name, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('cat_name', $this->cat_name, true);
        $criteria->compare('clinic_title', $this->clinic_title);
        $criteria->compare('academic_title', $this->academic_title);
        $criteria->compare('remote_domain', $this->remote_domain, true);
        $criteria->compare('remote_file_key', $this->remote_file_key, true);
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
     * @return NewDoctor the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
