<?php

namespace AngularBlog\Classes\Comment;

use AngularBlog\Classes\Models;
use AngularBlog\Interfaces\Constants as C;
USE AngularBlog\Interfaces\ModelsErrors AS Me;

class CommentList extends Models{
    
    private array $results = array(); //Array of Comment objects result
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_COMMENTS;
    }

    public function getResults():array {return $this->results;}
    public function getError(){
        if($this->errno <= Me::MODELS_RANGE_MAX){
            return parent::getError();
        }
        else{
            switch($this->errno){
                default:
                    $this->error = null;
                    break;
            }
        }
        return $this->error;
    }
}

?>