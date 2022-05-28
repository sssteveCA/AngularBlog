<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ArticleErrors as Ae;
use AngularBlog\Classes\Model;

class Article extends Model implements Ae,C{
    private ?string $id; //Unique id of the article
    private ?string $title;
    private ?string $author; //author id that created this article
    private ?string $permalink;
    private ?string $content;
    private string $introtext; //Text for quick article description
    private array $categories = array();
    private array $tags = array();
    private ?string $creation_time; //Date of creation
    private ?string $last_modified; //Date of last update
    private int $errno = 0; //Last error code
    private ?string $error = null; //Error message

    public function __construct(array $data)
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_ARTICLES;
        parent::__construct($data);
        $this->id = isset($data['id'])? $data['id']:null;
        $this->title = isset($data['title'])? $data['title']:null;
        $this->author = isset($data['author'])? $data['author']:null;
        $this->permalink = isset($data['permalink'])? $data['permalink']:null;
        $this->content = isset($data['content'])? $data['content']:null;
        $this->introttext = isset($data['introttext'])? $data['introttext']:null;
        $this->categories = isset($data['categories'])? $data['categories']:null;
        $this->tags = isset($data['tags'])? $data['tags']:null;
    }

    //getters
    public function getId() {return $this->id;}
    public function getTitle() {return $this->title;}
    public function getAuthor() {return $this->author;}
    public function getPermalink() {return $this->permalink;}
    public function getContent() {return $this->content;}
    public function getIntrotext() {return $this->introtext;}
    public function getCategories():array {return $this->categories;}
    public function getTags():array {return $this->tags;}
    public function getCrTime() {return $this->creation_time;}
    public function getLastMod() {return $this->last_modified;}
    public function getErrno():int {return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Ae::INVALIDDATAFORMAT:
                $this->error = Ae::INVALIDDATAFORMAT_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Insert a new article in the database
    public function article_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $this->creation_time = date('Y-m-d H:i:s');
        $this->last_modified = date('Y-m-d H:i:s');
        if($this->validate()){
            //All data are valid and can be inserted
            $values = [
                'title' => $this->title,
                'author' => $this->author,
                'permalink' => $this->permalink,
                'content' => $this->content,
                'introtext' => $this->introtext,
                'categories' => $this->categories,
                'tags' => $this->tags,
                'creation_time' => $this->creation_time,
                'last_modified' => $this->last_modified
            ];
            parent::create($values);
            if($this->errno == 0)$inserted = true;
        }//if($this->validate()){
        else
            $this->errno = Ae::INVALIDDATAFORMAT;
        return $inserted;

    }

    //Remove an article from the database
    public function article_delete(array $filter): bool{
        $deleted = false;
        $this->errno = 0;
        parent::delete($filter);
        if($this->errno == 0)$deleted = true;
        return $deleted;
    }

    //Get the first article that match with the filter
    public function article_get(array $filter): bool{
        $got = false;
        $this->errno = 0;
        $article = parent::get($filter);
        if($this->errno == 0){
            //Found an article with given filter
            $this->id = $article["_id"];
            $this->title = $article["title"];
            $this->author = $article["author"];
            $this->permalink = $article["permalink"];
            $this->content = $article["content"];
            $this->introtext = $article["introtext"];
            $this->categories = $article["categories"];
            $this->tags = $article["tags"];
            $this->creation_time = $article["creation_time"];
            $this->last_modified = $article["last_modified"];
            $got = true;
        }//if($this->errno == 0){
        return $got;
    }

    //Update the article 
    public function article_update(array $filter,array $data){
        $updated = false;
        $this->errno = 0;
        $data['last_modified'] = date('Y-m-d H:i:s');
        parent::update($filter,$data);
        if($this->errno == 0)$updated = true;
        return $updated;
    }

    //check if properties are all valid before insert
    private function validate(): bool{
        $valid = true;
        return $valid;
    }

}
?>