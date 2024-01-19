<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\ArticleList;
use Dotenv\Dotenv;
use MongoDB\BSON\Regex;
use Exception;

/**
 * JSON response for get articles list by query GET route
 */
class GetArticlesByQuery{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_MESSAGE => ''
        ];
        $get = $params['get'];
        if(isset($get['query']) && $get['query'] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $query = $get['query'];
                $al = new ArticleList();
                $regex = new Regex($query,'i');
                $filter = [ 'title' =>  $regex ];
                $found = $al->articlelist_get($filter);
                if($found){
                    //At least one article found
                    $articles = $al->getResults();
                    foreach($articles as $article){
                        $response[C::KEY_DATA][] = array(
                            'title' => $article->getTitle(),
                            'author' => $article->getAuthor(),
                            'permalink' => $article->getPermalink(),
                            'introtext' => $article->getIntrotext(),
                        );
                    }//foreach($articles as $article){
                    $response[C::KEY_DONE] = true;
                }
                else{
                    $response[C::KEY_EMPTY] = true;
                    $response[C::KEY_MESSAGE] = 'La ricerca di '.$query.' non ha fornito alcun risultato';
                }
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::SEARCH_ERROR;
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
        }
        return $response;
    }
}

?>