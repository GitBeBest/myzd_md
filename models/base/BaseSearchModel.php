<?php
namespace app\models\base;
use app\models\doctor\Doctor;
use yii\base\Model;

abstract class BaseSearchModel {

    /**
     * @var $model Doctor
     */
    public $model;
    public $alias = 't';
    public $select = '*';
    public $order;
    public $limit;
    public $offset;
    public $searchInputs;
    public $searchCondition;
    public $queryParams;
    public $join;
    public $distinct;
    public $output;
    public $query;

    /**
     * @return Doctor
     */
    abstract function model();

    //@abstract method to be implemented.
    abstract function addQueryConditions();

    /**
     * 
     * @param array $searchInputs
     */
    public function __construct(array $searchInputs, $with = null) {
        $this->searchInputs = $searchInputs;
        $this->with = $with;
        $this->limit = 10;
        $this->offset = 0;
        $this->searchCondition = [];
        $this->join = [];
        $this->distinct = false;
        $this->model();
        $this->prepareParameters();
        $this->buildSearchQuery();
    }

    public function search() {
        $this->createOutput();

        return $this->output;
    }

    public function count() {
        return count($this->output);
    }

    public function addSearchCondition($filed, $value=null) {
        $this->searchCondition[$filed] = $value;
    }

    //@Implement.
    /**
     * This method specifies the seach parameters in the request.
     * i.e. name, age.
     * @return array.
     */
    public function getQueryFields() {
        return array();
    }

    //@Implement.
    public function buildSearchQuery() {
        $this->setSelect($this->select);
        $this->addQueryConditions();
    }

    public function prepareParameters() {
        $this->parseSearchInputs();
        $this->parseQueryOptions($this->searchInputs);
    }

    protected function createOutput() {
        $model = $this->model;
        $query = $model::find()->alias($this->alias)->select($this->select)->where($this->searchCondition);
        if(arrayNotEmpty($this->join)) {
            foreach($this->join as $join) {
                $query->join($join[0], $join[1], $join[2]);
            }
        }
        $this->output = $query->orderBy($this->order)->limit($this->limit)->offset($this->offset)->all();
    }

    protected function parseSearchInputs() {
        $queryFields = $this->getQueryFields();
        if (arrayNotEmpty($queryFields)) {
            foreach ($queryFields as $field) {
                if (isset($this->queryParams[$field])) {
                    if (strIsEmpty($this->searchInputs[$field]) === false) {
                        $this->queryParams[$field] = trim($this->searchInputs[$field]);
                    }
                }
            }
        }
    }

    protected function parseQueryOptions($queryString) {
        // order by.
        if (isset($queryString['order'])) {
            $order = $queryString['order'];
        } else {
            $order = 't.id';
        }
        $this->setOrder($order);

        // limit of pageSize.
        if (isset($queryString['limit'])) {
            $limit = intval($queryString['limit']);
        } elseif (isset($queryString['page_size'])) {
            $limit = intval($queryString['page_size']);
        } else {
            $limit = $this->limit;
        }
        $this->setLimit($limit);

        // offset or page
        if (isset($queryString['offset'])) {
            $offset = intval($queryString['offset']);
        } elseif (isset($queryString['page'])) {
            $offset = (intval($queryString['page']) - 1) * $this->limit;
        } else {
            $offset = 0;
        }
        $this->setOffset($offset);
    }

    protected function hasQueryParams() {
        return arrayNotEmpty($this->queryParams);
    }

    /**
     * overwrite this method to customize select fields in the sql.
     * @param array $fields
     */
    public function setSelectFields(array $fields) {
        if (arrayNotEmpty($fields)) {
            foreach ($fields as &$field) {
                $field = $this->alias . '.' . $field;
            }
            $this->select = implode(',', $fields);
            $this->setSelect($this->select);
        }
    }

    public function setOrder($order) {
        if (strContains($order, '.')) {
            $this->order = $order;
        } else {
            $this->order = $this->alias . '.' . $order;
        }
    }

    public function setLimit($n) {
        $limit = intval($n);
        if ($limit < 0) {
            $limit = 0;
        }
        $this->limit = $limit;
    }

    public function setOffset($n) {
        $offset = intval($n);
        if ($offset < 0) {
            $offset = 0;
        }
        $this->offset = $offset;
    }

    public function setSelect($v) {
        $this->select = $v;
    }

}
