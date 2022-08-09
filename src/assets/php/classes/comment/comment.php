<?php

namespace AngularBlog\Classes\Comment;

use AngularBlog\Classes\Model;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Comment\CommentErrors as Ce;
use AngularBlog\Interfaces\Constants as C;
USE AngularBlog\Interfaces\ModelErrors AS mE;

class Comment extends Model implements Ce{

    use ErrorTrait;

    private ?string $id; //Unique id of the comment
    private ?string $article; //Article id where the comment was posted
    private ?string $author; //Author id that posts the comment
    private ?string $comment; //Comment text
    private ?string $creation_time; //The date where the comment wwas posted

    const OPERATION_GET = 1; 
    const OPERATION_CREATE = 2;
    const OPERATION_UPDATE = 3;
    const OPERATION_DELETE = 4;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_COMMENTS;
        parent::__construct($data);
        $this->id = isset($data['id']) ? $this->id = $data['id'] : null;
        $this->article = isset($data['article']) ? $this->article = $data['article'] : null;
        $this->author = isset($data['author']) ? $this->author = $data['author'] : null;
        $this->text = isset($data['text']) ? $this->text = $data['text'] : null;
        $this->date = isset($data['date']) ? $this->date = $data['date'] : null;
    }

    public function getError(){
        if($this->errno <= Me::MODEL_RANGE_MAX){
            //An error of superclass
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

    private function validate($operation): bool{
        $valid = true;
        if($operation == Comment::OPERATION_CREATE){
            //Validate data before insert
            if(!isset($this->article) || !is_numeric($this->article)){
                $valid = false;
            }
            if(!isset($this->author) || !is_numeric($this->author)){
                $valid = false;
            }
            if(!isset($this->text)){
                $valid = false;
            }
            if(!isset($this->article) || !is_numeric($this->article)){
                $valid = false;
            }
        }//if($operation == Comment::OPERATION_CREATE){
        else{
            //All orther operations required only the id
            if(!isset($this->id)){
                $valid = false;
            }
        }
        return $valid;
    }

}
?>