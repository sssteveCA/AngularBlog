<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Constants as C;
use AngularBLog\Interfaces\Article\ArticleListErrors as Ale;
use AngularBlog\Classes\Models;
use AngularBlog\Classes\Article\Article;

//This class is used to executed actions on multiple artciles at time
class ArticleList extends Models implements Ale,C{

    private array $results = array(); //Array of Article objects result
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_ARTICLES;
        parent::__construct($data);
    }

    public function getResults():array {return $this->results;}

    public function articlelist_get(array $filter): bool{
        $got = false;
        $cursor = parent::get($filter);
        if($this->errno == 0){
            $results = $cursor->toArray();
            foreach($results as $article){
                //Add Article object from values gotten
                $this->results[] = new Article($article);
            }
        }//if($this->errno == 0){
        file_put_contents(ArticleList::$logFile,"Articles => ".var_export($this->results,true)."\r\n");
        return $got;
    }
}
?>