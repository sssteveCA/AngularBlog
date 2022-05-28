<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelsErrors as Me;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use MongoDB\Driver\Cursor;

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
        $this->database = $this->h->{$this->database_name}; //Access to the database
        $this->collection = $this->database->{$this->collection_name};
    }

    public function __destruct()
    {
        
    }

    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    public function get(array $filter):Cursor{
        $this->errno = 0;
        $find = $this->collection->find($filter);
        //Check if there are results
        $l = sizeof($find->toArray());
        if($l <= 0)$this->errno = Me::NORESULT;
        return $find;
    }

}
?>