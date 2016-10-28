<?php
namespace app\models\region;
use app\models\base\BaseActiveRecord;

/**
 * Class RegionCountry
 * @package app\models\region
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $name_cn
 * @property integer $nest_level
 * @property string $description
 * @property string $phone_code
 * @property string $image_url
 * @property string $tn_url
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class RegionCountry extends BaseActiveRecord {
    const DEFAULT_IMAGE_PATH='location.jpg';

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return 'region_country';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['nest_level'], 'number', 'integerOnly' => true],
            [['code'], 'string', 'max' => 3],
            [['name', 'name_cn'], 'string','max' => 45],
            [['description, image_url, tn_url'],'string','max' => 255],
            [['phone_code'], 'string','max' => 5],
            [['date_created', 'date_updated','date_deleted'], 'string','safe']
        ];
    }

    public function getRegionState() {
        return $this->hasMany(RegionState::className(), ['id' => 'country_code'])->orderBy('display_order ASC');
    }

    public function getRegionCity() {
        return $this->hasMany(RegionCity::className(), ['id' => 'country_code'])->orderBy('display_order ASC');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'name_cn' => 'Name Cn',
            'nest_level' => 'Nest Level',
            'description' => 'Description',
            'phone_code' => 'Phone Code',
            'image_url' => 'Image Url',
            'tn_url' => 'Tn Url',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function getByCode($code, $with=null) {
        $model = $this->getByAttributes(array('code' => $code), $with);
        return $model;
    }

    public function getAbsoluteUrlDisplayPhoto($thumbnail=false) {
        if ($thumbnail && $this->getThumbnailUrl() !== null) {
            return $this->getAbsoluteThumbnailUrl();
        } else if ($this->getImageUrl() !== null) {
            return $this->getAbsoluteImageUrl();
        } else {
            return $this->getAbsoluteUrlDefaultImage();
        }
    }

    public function getAbsoluteImageUrl() {
        return \Yii::$app->request->getBaseUrl() . '/' . $this->getImageUrl();
    }

    public function getAbsoluteThumbnailUrl() {
        return \Yii::$app->request->getBaseUrl() . '/' . $this->getThumbnailUrl();
    }

    public static function getAbsoluteUrlDefaultImage() {
        return \Yii::$app->request->getBaseUrl() . '/' . \Yii::$app->params['regionImagePath'] . self::DEFAULT_IMAGE_PATH;
    }

    public function getStates() {
        if ($this->regionState === null) {
            $this->regionState = (new RegionState())->getAllByCountryCode($this->code);
        }
        return $this->rcStates;
    }


    public function getCode() {
        return $this->code;
    }

    public function getName($lang='cn') {
        if ($lang == 'cn')
            return $this->name_cn;
        else
            return $this->name;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function getThumbnailUrl() {
        return $this->tn_url;
    }

}
