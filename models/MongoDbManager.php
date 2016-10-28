<?php
namespace app\models;
class MongoDbManager
{
    private static $obj;
    private $mongodb;
    private $dbName;
    
    function __construct()
    {
        $config = Yii::app()->params->itemAt('mongodb');
        if(!empty($config)) {
            $this->dbName = $config['dbName'];
            $this->mongodb = new MongoDB\Driver\Manager($config['connectionString']);
        }
        else {
            $errorMessage = 'MongoDb config load error';
            Yii::log($errorMessage, CLogger::LEVEL_ERROR, __METHOD__);
            throw new InvalidArgumentException($errorMessage);
        }

    }
    
    public function queryAll()
    {
        $data = $this->mongodb->executeQuery($this->dbName, new MongoDB\Driver\Query([]), new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED))->toArray();
        
        return empty($data) ? false : $data;
    }
    
    public function insert(Array $params)
    {

        
        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        $bulk->insert($params);
        
        $result = $this->mongodb->executeBulkWrite($this->dbName, $bulk, new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000));
        $count = $result->getInsertedCount() || $result->getModifiedCount() ? 1 : 0;
        
        return $count;
    }
}      
