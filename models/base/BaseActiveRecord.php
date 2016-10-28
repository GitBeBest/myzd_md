<?php
namespace app\models\base;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class BaseActiveRecord
 * @package app\models\base
 *
 * @property integer $id
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
abstract class BaseActiveRecord extends ActiveRecord {

    const DB_FORMAT_DATETIME = 'Y-m-d H:i:s';
    const DB_FORMAT_DATE = 'Y-m-d';

    /**
     * Prepares date_created, date_updated.
      id attributes before performing validation.
     */
    public function beforeValidate() {
        $now = new Expression("NOW()");         // date_created, date_updated.
        if ($this->isNewRecord) {
            $this->setDateCreated($now);
        }
        $this->setDateUpdated($now);
        
        $attributes = $this->trimAttributes();
        if(is_array($attributes) && count($attributes)>0){
            foreach($attributes as $attribute){
                $this->{$attribute}=trim($this->{$attribute});
            }
        }
        return parent::beforeValidate();
    }

    // Change datetime format after record is queried from db.
    public function afterFind() {
        parent::afterFind();
    }
    
    protected function trimAttributes(){
        return array();
    }


    public function getSafeAttributes() {
        $safeAttributeNames = $this->getSafeAttributes();
        return $this->getAttributes($safeAttributeNames);
    }

    /**
     * 
     * @return array the first error of each attribute.
     */
    public function getFirstErrors() {
        $ret = array();
        $errorList = $this->getErrors();
        if (emptyArray($errorList) === false) {
            foreach ($errorList as $attribute => $errors) {
                if (emptyArray($errors) === false) {
                    $error = array_shift($errors);
                    $ret[$attribute] = $error;
                }
            }
        }
        return $ret;
    }

    protected function dateToDBFormat($dateStr) {
        $date = new \DateTime($dateStr);
        if ($date === false)
            return null;
        else
            return $date->format(self::DB_FORMAT_DATE);
    }

    protected function datetimeToDBFormat($dateStr) {
        $date = new \DateTime($dateStr);
        if ($date === false) {
            return null;
        } else
            return $date->format(self::DB_FORMAT_DATETIME);
    }

    protected function getDateAttribute($dateStr, $format = null) {
        if (empty($dateStr)) {
            return null;
        }
        if (is_null($format)) {
            $format = 'Y年m月d日';
        }
        $date = new \DateTime($dateStr);
        return $date->format($format);
    }

    protected function setDateAttribute($dateStr) {
        if (empty($dateStr)) {
            return null;
        } else {
            return $this->dateToDBFormat($dateStr);
        }
    }

    protected function getDatetimeAttribute($datetimeStr, $format = null) {
        if (empty($datetimeStr)) {
            return null;
        }
        if (is_null($format)) {
            $format = 'Y年m月d日 H:i';
        }
        $datetime = new \DateTime($datetimeStr);
        return $datetime->format($format);
    }

    protected function setDatetimeAttribute($datetimeStr) {
        if (empty($datetimeStr)) {
            return null;
        } else {
            return $this->datetimeToDBFormat($datetimeStr);
        }
    }

    protected function getTextAttribute($value, $nText = true) {
        if ($nText) {
            if($value === null) {
                $value = '';
            }
            return \Yii::$app->formatter->format($value,'Ntext');
        } else {
            return $value;
        }
    }

    protected function getNullAttribute($value, $nullStr = "") {
        if (is_null($value)) {
            return $nullStr;
        } else {
            return $value;
        }
    }

    protected function getBooleanAttribute($value) {
        return ($value == 1 || $value === true);
    }

    public function setEmptyAttributeToNull($attribute) {
        if (empty($this->{$attribute}) || $this->{$attribute} == '')
            $this->{$attribute} = null;
    }

    /*     * ****** Query Methods ******* */
    /*
     * Override parent implementation.
     */

    public function findByPk($pk, $condition = '', $params = array()) {
        if ($pk === null) {
            return null;
        } else {
            return parent::findOne($pk);
        }
    }

    /**
     * order by ids => 'order'=>FIELD(id, 2,3,1).
     * @param array $ids
     * @param type $with
     * @return $this
     */
    public function getAllByIds($ids, $with = null) {
        if (is_array($ids) === false) {
            $ids = array($ids);
        }
        $result = $this::find()->alias('t')
            ->where(['t.date_deleted' => null])
            ->andWhere(['in', 't.id', $ids])
            ->orderBy("FIELD(t.id," . arrayToCsv($ids) . ")")
            ->all();
        return $result;
    }

    /*
     * @param $field string the column to be compared.
     * @param $values array values to be passed into "IN" condition.
     * Sample Query: select * from table where $field IN $values.
     */

    public function getAllByInCondition($field, $values, $with = null) {
        if (is_array($values) === false) {
            $values = array($values);
        }
        $result =$this::find()->alias('t')
            ->where(['t.date_deleted' => null])
            ->andWhere(['in', $field, $values])
            ->all();
        return $result;
    }

    public function getAll($with = null, $options = null) {

    }

    /**
     * @param $id
     * @param null $with
     * @return array|null|$this
     */
    public function getById($id, $with = null) {
        if (is_null($id))
            return null;
        else if (is_array($with)) {
            return $this->find()->alias('t')->joinWith($with)->where(['t.id' => $id, 't.date_deleted' => null])->one();
        } else {
            return $this->find()->where(['id' => $id, 'date_deleted' => null])->one();
        }
    }

    /**
     *  Query model with relations, 'date_deleted' is null by default if not specified.
     * @param array $attr
     * @param type $with array of model's relations.
     * @return $this
     */
    public function getByAttributes(array $attr, $with = null) {
        if (isset($attr['t.date_deleted']) === false)
            $attr['t.date_deleted'] = null;
        if (is_array($with)){
            return $this::find()->alias('t')->joinWith($with)->where($attr)->one();
        }
        else{
            return $this::find()->alias('t')->where($attr)->one();
        }
    }

    /**
     * @param array $attr
     * @param null $with
     * @param null $options
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllByAttributes(array $attr, $with = null, $options = null) {

        $condition['t.date_deleted'] = null;
        $models = $this::find()->alias('t')->where(['t.date_deleted' => null]);
        foreach ($attr as $key => $value) {
            $models->andWhere([$key => $value]);
        }
        if (isset($with) && is_array($with)) {
            $models->joinWith($with);
        }
        if (isset($options['order'])) {
            $models->orderBy($options['order']);
        }
        if (isset($options['offset'])) {
            $offset = $options['offset'];
            $models->offset($options['offset']);
        }
        $limit = 10;
        if (isset($options['limit'])) {
            $limit = $options['limit'];
        }

        $models->limit($limit);

        return $models->all();
    }

    /*
     * Mark record as deleted (date_deleted is not null).
     */

    public function delete($absolute = true) {
        if ($absolute) {
            return parent::delete();
        } else {
            if (!$this->getIsNewRecord()) {
                \Yii::trace(get_class($this) . '.delete()', 'system.db.ar.CActiveRecord');
                $now = new Expression('NOW()');
                $model = $this->findByPk($this->id);
                $model->date_deleted = $now;
                return $model->update();
            } else
                throw new Exception(\Yii::t('yii', 'The active record cannot be deleted because it is new.'));
        }
    }

    /**
     * @param array $fields
     * @param array $attr
     * @return int
     */
    public function updateAllByAttributes(array $fields, array $attr) {
        if(is_array($attr) && count($attr) > 0) {
            foreach($attr as $key => $value) {
                if(isset($this->$key) && isset($fields[$key])) {
                    $this->$key = $value;
                }
            }
        }
        return $this->update();
    }

    /*     * ****** Accessors ******* */

    public function getId() {
        return ($this->id);
    }

    public function setId($v) {
        $this->id = $v;
    }

    public function getDateCreated($format = 'Y年m月d日 H:i:s') {
        $date = new \DateTime($this->date_created);
        return $date->format($format);
    }

    private function setDateCreated($v) {
        $this->date_created = $v;
    }

    public function getDateUpdated($format = 'Y年m月d日 h:i:s') {
        $date = new \DateTime($this->date_updated);
        return $date->format($format);
    }

    private function setDateUpdated($v) {
        $this->date_updated = $v;
    }

    public function getDateDeleted($format = 'Y年m月d日 h:i:s') {
        $date = new \DateTime($this->date_deleted);
        return $date->format($format);
    }

    private function setDateDeleted($v) {
        $this->date_deleted = $v;
    }

}
