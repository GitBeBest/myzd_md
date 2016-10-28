<?php
namespace app\models\region;
use app\models\base\BaseActiveRecord;

/**
 * Class RegionCity
 * @package app\models\region
 *
 * @property integer $id
 * @property string $country_name
 * @property string $country_code
 * @property string $state_name
 * @property integer $state_id
 * @property string $code
 * @property string $name
 * @property string $name_cn
 * @property string $description
 * @property string $image_url
 * @property string $tn_url
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class RegionCity extends BaseActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'region_city';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['state_id'], 'number', 'integerOnly' => true],
            [['country_name', 'state_name', 'name', 'name_cn'], 'string', 'max' => 45],
            [['country_code'], 'string', 'max' => 3],
            [['code'], 'string', 'max' => 20],
            [['description', 'image_url', 'tn_url'], 'string', 'max' => 255],
            [['date_created', 'date_updated', 'date_deleted'], 'safe']
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'country_name' => 'Country Name',
            'country_code' => 'Country Code',
            'state_name' => 'State Name',
            'state_id' => 'State',
            'code' => 'Code',
            'name' => 'Name',
            'name_cn' => 'Name Cn',
            'description' => 'Description',
            'image_url' => 'Image Url',
            'tn_url' => 'Tn Url',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /*     * ****** Query Methods ******* */

    public function getAllByCountryCode($code) {
        $result = $this->find()->where(['date_deleted' => null, 'country_code' => $code])
            ->orderBy('display_order')->all();

        return $result;
    }

    public function getAllByStateId($stateId) {
        $models = $this->getAllByAttributes(array('state_id' => $stateId));
        return $models;
    }

    public function getListCityByStateId($stateId){
        $result = $this->find()->select(['id', 'name'])
            ->where(['date_deleted' => null, 'state_id' => $stateId])
            ->orderBy('display_order ASC')
            ->all();
        return $result;
    }
    
    public function getListCityByCountryId($countryId){
        $result = $this->find()->select(['id', 'name'])
            ->where(['date_deleted' => null, 'country_id' => $countryId])
            ->orderBy('display_order ASC')
            ->all();
        return $result;
    }

    public function getAbsoluteUrlDisplayPhoto($thumbnail=false) {
        if (isset($this->cityDisplayPhoto)) {
            if ($thumbnail) {
                return $this->cityDisplayPhoto->getAbsoluteThumbnailUrl();
            } else {
                return $this->cityDisplayPhoto->getAbsoluteImageUrl();
            }
        }else
            return RegionCityImage::getAbsoluteUrlDefaultImage();
    }

    /*     * ****** Accessors ****** */

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
