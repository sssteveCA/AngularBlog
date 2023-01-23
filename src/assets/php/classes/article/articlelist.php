<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelsErrors as Me;
use AngularBLog\Interfaces\Article\ArticleListErrors as Ale;
use AngularBlog\Classes\Models;
use AngularBlog\Classes\Article\Article;

//This class is used to executed actions on multiple artciles at time
class ArticleList extends Models implements Ale,C,Me{

    private array $results = array(); //Array of Article objects result
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: $_ENV['MONGODB_CONNECTION_STRING'];
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: $_ENV['MONGODB_DATABASE'];
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: $_ENV['MONGODB_COLLECTION_ARTICLES'];
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

    public function articlelist_get(array $filter, array $options = []): bool{
        file_put_contents(ArticleList::$logFile,"ArticleList get \r\n",FILE_APPEND);
        $got = false;
        $this->errno = 0;
        $cursor = parent::get($filter,$options);
        //file_put_contents(ArticleList::$logFile,"ArticleList get => ".var_export($cursor,true)."\r\n",FILE_APPEND);
        if($this->errno == 0){
            $results = $cursor->toArray();
            //file_put_contents(ArticleList::$logFile,"ArticleList results array => ".var_export($results,true)."\r\n",FILE_APPEND);
            foreach($results as $article){
                //Add Article object from values gotten
                $categories = $article['categories']->bsonSerialize();
                $tags = $article['tags']->bsonSerialize();
                //file_put_contents(ArticleList::$logFile,"ArticleList results categories => ".var_export($categories,true)."\r\n",FILE_APPEND);
                //file_put_contents(ArticleList::$logFile,"ArticleList results tags => ".var_export($tags,true)."\r\n",FILE_APPEND);
                $data = [
                    "id" => $article["_id"],
                    "title" => $article["title"],
                    "author" => $article["author"],
                    "permalink" => $article["permalink"],
                    "content" => $article["content"],
                    "introtext" => $article["introtext"],
                    "categories" => $categories,
                    "tags" => $tags,
                    "creation_time" => $article["creation_time"],
                    "last_modified" => $article["last_modified"], 
                ];
                $this->results[] = new Article($data);
            }
            $got = true;
        }//if($this->errno == 0){
        //file_put_contents(ArticleList::$logFile,"Articles => ".var_export($this->results,true)."\r\n",FILE_APPEND);
        return $got;
    }
}
?>