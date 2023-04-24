<?php

namespace AngularBlog\Classes\Comment;

use AngularBlog\Classes\Models;
use AngularBlog\Interfaces\Constants as C;
USE AngularBlog\Interfaces\ModelsErrors AS Me;
use AngularBlog\Interfaces\Article\Comment\CommentListErrors as Cle;

class CommentList extends Models implements Cle{
    
    private array $results = array(); //Array of Comment objects result
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: $_ENV['MONGODB_CONNECTION_STRING'];
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: $_ENV['MONGODB_DATABASE'];
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: $_ENV['MONGODB_COLLECTION_COMMENTS'];
        parent::__construct($data);
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

    /**
     * Delete one or more comments that match the filter
     */
    public function commentlist_delete(array $filter): bool{
        $this->errno = 0;
        parent::delete($filter);
        if($this->errno == 0) return true;
        return false;
    }

    /**
     * Get one or more comments that match the filter
     */
    public function commentlist_get(array $filter): bool{
        $this->errno = 0;
        $cursor = parent::get($filter);
        if($this->errno == 0){
            //Superclass get does not return any error
            $results = $cursor->toArray();
            foreach($results as $comment){
                $data = [
                    "id" => $comment["_id"],
                    "article" => $comment["article"],
                    "author" => $comment["author"],
                    "comment" => $comment["comment"],
                    "creation_time" => $comment["creation_time"],
                    "last_modified" => $comment["last_modified"]
                ];
                $this->results[] = new Comment($data);
            }//foreach($results as $comment){
            return true;
        }//if($this->errno == 0){
        return false;
    }
}

?>