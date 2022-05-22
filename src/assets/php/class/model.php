<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\DeleteResult;
use MongoDB\InsertOneResult;
use MongoDB\Model\BSONDocument;
use MongoDB\UpdateResult;

//Base class that interfaces with MongoDB database
abstract class Model implements C{
    private ?string $connection_url = null;
    private ?string $database_name = null;
    private ?string $collection_name = null;
    protected ?Client $h = null; //MongoDB connection handle 
    protected bool $connect = false; //true if there is a MongoDB connection
    protected ?Database $database ; //MongoDB database used by this class
    protected ?Collection $collection; //MongoDB collection of registered users
    protected static string $logFile = C::FILE_LOG;

    public function __constructor(array $data){
        $this->connection_url = isset($data['connection_url'])? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $this->database_name = isset($data['database_name'])? $data['database_name']: C::MONGODB_DATABASE;
        $this->collection_name = isset($data['collection_name'])? $data['collection_name']: C::MONGODB_COLLECTION_USERS;
        $this->h = new Client($this->connection_url);
        $this->database = $this->h->{$this->database_name}; //Access to the database
        $this->collection = $this->database->{$this->collection_name};
    }

    public function __destruct()
    {
        
    }


    //Get one document with given array filter
    public function get(array $filter): BSONDocument{
        $findOne = $this->collection->findOne($filter);
        return $findOne;
    }

    //Create one document and insert it
    public function create(array $data): InsertOneResult{
        $insertOne = $this->collection->insertOne($data);
        return $insertOne;
    }

    //Update one document that match a filter with data
    public function update(array $filter, array $data): UpdateResult{
        $updateOne = $this->collection->updateOne($filter,$data);
        return $updateOne;
    }

    //Delete one document that match with a filter
    public function delete(array $filter): DeleteResult{
        $deleteOne = $this->collection->deleteOne($filter);
        return $deleteOne;
    }
}
?>