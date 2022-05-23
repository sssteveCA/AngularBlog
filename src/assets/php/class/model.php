<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelErrors as Me;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\DeleteResult;
use MongoDB\InsertOneResult;
use MongoDB\Model\BSONDocument;
use MongoDB\UpdateResult;

//Base class that interfaces with MongoDB database
abstract class Model implements C,Me{
    private ?string $connection_url = null;
    private ?string $database_name = null;
    private ?string $collection_name = null;
    protected ?Client $h = null; //MongoDB connection handle 
    protected bool $connect = false; //true if there is a MongoDB connection
    protected ?Database $database ; //MongoDB database used by this class
    protected ?Collection $collection; //MongoDB collection of registered users
    protected int $errno = 0; //error code
    protected ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public function __constructor(array $data){
        if(!isset($data['connection_url']))throw new \Exception(Me::CONNECTION_URL_EXC);
        if(!isset($data['database_name']))throw new \Exception(Me::DATABASE_NAME_EXC);
        if(!isset($data['collection_name']))throw new \Exception(Me::COLLECTION_NAME_EXC);
        $this->connection_url = $data['connection_url'];
        $this->database_name = $data['database_name'];
        $this->collection_name =  $data['collection_name'];
        $this->h = new Client($this->connection_url);
        $this->database = $this->h->{$this->database_name}; //Access to the database
        $this->collection = $this->database->{$this->collection_name};
    }

    public function __destruct()
    {
        
    }

    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Me::NORESULT:
                $this->error = Me::NORESULT_MSG;
                break;
            case Me::NOTCREATED:
                $this->error = Me::NOTCREATED_MSG;
                break;
            case Me::NOTUPDATED:
                $this->error = Me::NOTUPDATED_MSG;
                break;
            case Me::NOTDELETED:
                $this->error = mE::NOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }


    //Get one document with given array filter
    public function get(array $filter): BSONDocument{
        $this->errno = 0;
        $findOne = $this->collection->findOne($filter);
        if(!$findOne)$this->errno = Me::NORESULT;
        return $findOne;
    }

    //Create one document and insert it
    public function create(array $data): InsertOneResult{
        $this->errno = 0;
        $insertOne = $this->collection->insertOne($data);
        $count = $insertOne->getInsertedCount();
        if($count <= 0)$this->errno = Me::NOTCREATED;
        return $insertOne;
    }

    //Update one document that match a filter with data
    public function update(array $filter, array $data): UpdateResult{
        $this->errno = 0;
        $updateOne = $this->collection->updateOne($filter,$data);
        $matched = $updateOne->getMatchedCount();
        $updated = $updateOne->getModifiedCount();
        if(!($matched > 0 && $updated > 0))$this->errno = Me::NOTUPDATED;
        return $updateOne;
    }

    //Delete one document that match with a filter
    public function delete(array $filter): DeleteResult{
        $this->errno = 0;
        $deleteOne = $this->collection->deleteOne($filter);
        $deleted = $deleteOne->getDeletedCount();
        if($deleted <= 0)$this->errno = Me::NOTDELETED;
        return $deleteOne;
    }
}
?>