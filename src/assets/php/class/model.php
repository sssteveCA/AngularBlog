<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

//Base class that interfaces with MongoDB database
abstract class Model implements C{
    private ?string $connection_url = null;
    private ?string $database_name = null;
    private ?string $collection_name = null;
    protected ?Client $h = null; //MongoDB connection handle 
    protected bool $connect = false; //true if there is a MongoDB connection
    protected ?Database $database ; //MongoDB database used by this class
    protected ?Collection $collection; //MongoDB collection of registered users
    protected int $errno = 0; //error code
    protected ?string $error = null;
    protected static string $logFile = C::FILE_LOG;

    public function __constructor(array $data){
        $this->connection_url = isset($data['connection_url'])? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $this->database_name = isset($data['database_name'])? $data['database_name']: C::MONGODB_DATABASE;
        $this->collection_name = isset($data['collection_name'])? $data['collection_name']: C::MONGODB_COLLECTION_USERS;
        $this->h = new Client();
        $this->database = $this->h->{$this->database_name}; //Access to the database
        $this->collection = $this->database->{$this->collection};
    }

    public function __destruct()
    {
        
    }

    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
        }
        return $this->error;
    }
}
?>