<?php

namespace AngularBlog\Classes\Article;

use AngularBLog\Interfaces\Article\ArticleListErrors as Ale;
use AngularBlog\Classes\Models;
use AngularBlog\Classes\Article\Article;

//This class is used to executed actions on multiple artciles at time
class ArticleList extends Models implements Ale{

    private array $results = array(); //Array of Article objects result

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
        return $got;
    }
}
?>