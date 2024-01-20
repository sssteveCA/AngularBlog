<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\CommentList;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;
use Exception;

/**
 * JSON response for article comments GET route
 */
class ArticleComments{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_MESSAGE => '', C::KEY_DONE => false,'comments' => [],C::KEY_EMPTY => false,'error' => false
        ];
        $headers = $params['headers'];
        $get = $params['get'];
        if(isset($get['permalink']) && $get['permalink'] != '' && $get['permalink'] != 'undefined'){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $permalink = $get['permalink'];
                $token = token_exists($headers);
                $article = new Article();
                $filter = [ 'permalink' => $permalink ];
                $article_found = $article->article_get($filter);
                if($article_found){
                    $article_id = $article->getId();
                    $cl = new CommentList();
                    $filter = [ "article" => new ObjectId($article_id) ];
                    $comments_found = $cl->commentlist_get($filter);
                    if($comments_found){
                        //At least one comment found
                        $comments = $cl->getResults();
                        comments_loop($token,$comments,$response);
                    }//if($comments_found){
                    else{
                        $response[C::KEY_EMPTY] = true;
                        $response[C::KEY_MESSAGE] = C::COMMENTLIST_EMPTY;
                    }
                    $response[C::KEY_DONE] = true;
                }
                else{
                    $response[C::KEY_CODE] = 500;
                    $response['error'] = true;
                    $response[C::KEY_MESSAGE] = C::COMMENTLIST_ERROR;
                }  
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response['error'] = true;
                $response[C::KEY_MESSAGE] = C::COMMENTLIST_ERROR;
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