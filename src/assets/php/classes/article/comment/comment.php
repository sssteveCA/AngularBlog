<?php

namespace AngularBlog\Classes\Comment;

use AngularBlog\Classes\Model;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Article\Comment\CommentErrors as Ce;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelErrors AS mE;
use MongoDB\BSON\ObjectId;

class Comment extends Model implements Ce{

    use ErrorTrait;

    private ?string $id; //Unique id of the comment
    private ?string $article; //Article id where the comment was posted
    private ?string $author; //Author id that posts the comment
    private ?string $comment; //Comment text
    private ?string $creation_time; //The date where the comment wwas posted
    private ?string $last_modified; //The date where the comment was updated last time

    private static $logFile = C::FILE_LOG;

    const OPERATION_GET = 1; 
    const OPERATION_CREATE = 2;
    const OPERATION_UPDATE = 3;
    const OPERATION_DELETE = 4;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: $_ENV['MONGODB_CONNECTION_STRING'];
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: $_ENV['MONGODB_DATABASE'];
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: $_ENV['MONGODB_COLLECTION_COMMENTS'];
        parent::__construct($data);
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->article = isset($data['article']) ? $data['article'] : null;
        $this->author = isset($data['author']) ? $data['author'] : null;
        $this->comment = isset($data['comment']) ? $data['comment'] : null;
        $this->creation_time = isset($data['creation_time']) ? $data['creation_time'] : null;
        $this->last_modified = isset($data['last_modified']) ?  $data['last_modified'] : null;
    }

    public function getId(){return $this->id;}
    public function getArticle(){return $this->article;}
    public function getAuthor(){return $this->author;}
    public function getComment(){return $this->comment;}
    public function getCrTime(){return $this->creation_time;}
    public function getLastMod() {return $this->last_modified;}
    public function getError(){
        if($this->errno <= Me::MODEL_RANGE_MAX){
            //An error of superclass
            return parent::getError();
        }
        else{
            switch($this->errno){
                case Ce::INVALIDDATAFORMAT:
                    $this->error = Ce::INVALIDDATAFORMAT_MSG;
                    break;
                default:
                    $this->error = null;
                    break;
            }
        }
        return $this->error;
    }

    //Insert new comment in the database
    public function comment_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $this->creation_time = date('Y-m-d H:i:s');
        $this->last_modified = date('Y-m-d H:i:s');
        $validate = $this->validate(Comment::OPERATION_CREATE);
        if($validate){
            //All data are valid and can be inserted
            $values = [
                'article' => new ObjectId($this->article),
                'author' => new ObjectId($this->author),
                'comment' => $this->comment,
                'creation_time' => $this->creation_time,
                'last_modified' => $this->last_modified
            ];
            parent::create($values);
            if($this->errno == 0)$inserted = true;
        }//if($validate){
        else 
            $this->errno = Ce::INVALIDDATAFORMAT;
        return $inserted;
    }

    //Remove a comment from the database
    public function comment_delete(array $filter): bool{
        $deleted = false;
        $this->errno = 0;
        parent::delete($filter);
        if($this->errno == 0)$deleted = true;
        return $deleted;
    }

    //Get the first comment that match with the filter
    public function comment_get(array $filter): bool{
        $got = false;
        $this->errno = 0;
        $comment = parent::get($filter);
        if($this->errno == 0){
            $this->id = $comment["_id"];
            $this->article = $comment["article"];
            $this->author = $comment["author"];
            $this->comment = $comment["comment"];
            $this->creation_time = $comment["creation_time"];
            $this->last_modified = $comment["last_modified"];
            $got = true;
        }//if($this->errno == 0){
        return $got;
    }

    //Update the comment
    public function comment_update(array $filter,array $data): bool{
        $updated = false;
        $this->errno = 0;
        //Set last modified date before update
        $data['$set']['last_modified'] = date('Y-m-d H:i:s');
        parent::update($filter,$data);
        if($this->errno == 0)$updated = true;
        return $updated;
    }

    private function validate($operation): bool{
        $valid = true;
        if($operation == Comment::OPERATION_CREATE){
            //Validate data before insert
            if(!isset($this->article)){
                $valid = false;
            }
            if(!isset($this->author)){
                $valid = false;
            }
            if(!isset($this->comment)){
                $valid = false;
            }
            if(!isset($this->article)){
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