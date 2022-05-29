<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelsErrors as Me;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use MongoDB\Driver\Cursor;
use MongoDB\InsertManyResult;
use MongoDB\UpdateResult;
use MongoDB\DeleteResult;

//This class execute operation on multiple document in the collection
abstract class Models implements C,Me{
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

    public function __construct(array $data)
    {
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
                $this->error = Me::NOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Get one or more documents
    public function get(array $filter):Cursor{
        file_put_contents(Models::$logFile,"Models get => \r\n");
        $this->errno = 0;
        $find = $this->collection->find($filter);
        file_put_contents(Models::$logFile,"Find => ".var_export($find,true)."\r\n");
        //Check if there are results
        $l = sizeof($find->toArray());
        if($l <= 0)$this->errno = Me::NORESULT;
        return $find;
    }

    //Create one or more documents
    public function create(array $filter): InsertManyResult{
        $this->errno = 0;
        $insertMany = $this->collection->insertMany($filter);
        $count = $insertMany->getInsertedCount();
        if($count <= 0)$this->errno = Me::NOTCREATED;
        return $insertMany;
    }

    //Update one or more documents
    public function update(array $filter,array $data): UpdateResult{
        $this->errno = 0;
        $updateMany = $this->collection->updateMany($filter,$data);
        $matched = $updateMany->getMatchedCount();
        $updated = $updateMany->getModifiedCount();
        if(!($matched > 0 && $updated > 0))$this->errno = Me::NOTUPDATED;
        return $updateMany;
    }

    //Delete one or more documents
    public function delete(array $filter): DeleteResult{
        $this->errno = 0;
        $deleteMany = $this->collection->deleteMany($filter);
        $count = $deleteMany->getDeletedCount();
        if($count <= 0)$this->errno = Me::NOTDELETED;
        return $deleteMany;
    }
    

}
?>