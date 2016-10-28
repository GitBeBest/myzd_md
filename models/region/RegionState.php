<?php
namespace app\models\region;
use app\models\base\BaseActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "region_state".
 *
 * The followings are the available columns in table 'region_state':
 *
 * @property integer $id
 * @property string $country_name
 * @property string $country_code
 * @property string $code
 * @property string $name
 * @property string $name_cn
 * @property integer $nest_level
 * @property string $description
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class RegionState extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'region_state';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['nest_level'], 'number', 'integerOnly' => true],
            [['country_name', 'code', 'name', 'name_cn'], 'string', 'max' => 45],
            [['country_code'], 'string', 'max' => 3],
            [['description'], 'string', 'max' => 255],
            [['date_created', 'date_updated', 'date_deleted'], 'safe']
        ];
    }


    public function getCities() {
        return $this->hasMany(RegionCity::className(), ['state_id' => 'id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'country_name' => 'Country Name',
            'country_code' => 'Country Code',
            'code' => 'Code',
            'name' => 'Name',
            'name_cn' => 'Name Cn',
            'nest_level' => 'Nest Level',
            'description' => 'Description',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }


    public function getAllByCountryCode($code) {
        return $this->find()->where(['date_deleted' => null, 'country_code' => $code])
            ->orderBy('display_order')
            ->all();
    }

    public function getAllByCountryId($id) {
        return $this->getAllByAttributes(array('country_id' => $id));
    }
    
    public function getListStateByCountryId($countryId){
//        $criteria = new CDbCriteria();
//        $criteria->select="id,name";
//        $criteria->addCondition("t.date_deleted is NULL");
//        $criteria->order="t.display_order ASC";
//        $criteria->compare("country_id", $countryId);
//        $criteria->distinct = true;
//
//        return $this->findAll($criteria);
    }

    public function getName($lang='cn') {
        if ($lang == 'cn')
            return $this->name_cn;
        else
            return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

}
