<?php
namespace app\models;
use app\models\region\RegionCity;
use app\models\region\RegionCountry;
use app\models\region\RegionState;
use yii\web\HttpException;

class RegionManager {
    /**
     * @param $id
     * @param null $with
     * @return array|null|\yii\db\ActiveRecord
     * @throws HttpException
     */
    public function loadLocationCity($id, $with = null) {
        $model = (new RegionCity())->getById($id, $with);
        if ($model === null) {
            throw new HttpException(404, 'Record is not found.');
        }
        return $model;
    }

    /**
     * @param $code
     * @param null $with
     * @return RegionCountry
     */
    public function getCountryByCode($code, $with = null) {
        $country = (new RegionCountry())->getByCode($code, $with);
        return $country;
    }

    /**
     * @param $stateId
     * @param null $with
     * @return array|null|RegionState
     */
    public function getStateById($stateId, $with = null) {
        $state = (new RegionState())->getById($stateId, $with);
        return $state;
    }

    public function getAllStatesByCountryCode($code) {
        $country = $this->getCountryByCode($code, array('regionState'));
        if (isset($country)) {
            return $country->getStates();
        } else {
            return null;
        }
    }

    public function getAllStatesByCountryId($id) {
        $country = (new RegionCountry())->getById($id, array('regionState'));
        if (isset($country)) {
            return $country->getStates();
        } else {
            return null;
        }
    }

    public function getAllCitiesByStateId($stateId) {
        $state = $this->getStateById($stateId, array('cities'));
        if (isset($state)) {
            return $state->cities;
        }
        return null;
    }

    /**
     * @param $code
     * @return null
     */
    public function getAllCitiesByCountryCode($code) {
        $country = $this->getCountryByCode($code, array('regionCity'));
        if (isset($country))
            return $country->getCities();
        else
            return null;
    }

    public function getAllCitiesByIds($idList) {
        return (new RegionCity())->getAllByIds($idList);
    }

    /**
     * search by exact match of city.name_cn or city.name.
     * @param type $name of RegionCity.
     * @return type aray of RegionCity models
     */
    public function getCityByExactName($name) {
        $result = (new RegionCity())->find()
            ->where(['date_deleted' => null])
            ->andWhere(['or', 'name= :name', 'name_cn=:name'],[':name' => $name])
            ->orderBy('display_order asc')
            ->all();
        return $result;
    }

    /**
     * search by like '%$name$'.
     * any record city.name, city.name_cn, city.code contains the $name will be returned.
     * @param type $name name of city.
     * @return type array of City models
     */
    public function getCitiesBySimilarName($name) {
        $result = (new RegionCity())->find()
            ->where(['date_deleted' => null])
            ->andWhere(['or', ['like', 'name', $name], ['like', 'name_cn', $name]], ['code' => $name])
            ->orderBy('display_order asc')
            ->all();

        return $result;
    }

    public function getCountryByExactName($name) {
        $result = (new RegionCountry())->find()
            ->where(['date_deleted' => null])
            ->andWhere(['or', "name=$name", "name_cn=$name", "alias=$name"])
            ->orderBy('display_order asc')
            ->all();
        return $result;
    }

    /**
     * @param $name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCitiesStartWithName($name) {
        $result = (new RegionCity())->find()
            ->where(['date_deleted' => null])
            ->andWhere(['or', "name like '$name%'", "name_cn like '$name%'", "code='$name'"])
            ->orderBy('display_order asc')
            ->all();

        return $result;
    }

    /**
     * @param $name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCountriesStartWithName($name) {
        $result = (new RegionCountry())->find()
            ->where(['date_deleted' => null])
            ->andWhere(['or', "name like '$name%'", "name_cn like '$name%'", "alias like '$name%'", "code='$name'"])
            ->orderBy('display_order asc')
            ->all();
        return $result;
    }

    public function getAllCountry() {
        $result = (new RegionCountry())->find()
            ->where(['date_deleted' => null])
            ->orderBy('display_order ASC')
            ->all();
        return $result;
    }

    public function loadRegionStateById($id, $attributes = null, $with = null) {
        return (new RegionState())->getById($id);
    }

    /**
     * @param $id
     * @param null $attributes
     * @param null $with
     * @return RegionCity
     */
    public function loadRegionCityById($id, $attributes = null, $with = null) {
        return  (new RegionCity())->getById($id);
    }

}
