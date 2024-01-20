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
                $token = self::tokenExists($headers);
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
                        self::commentsLoop($token,$comments,$response);
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

    protected static function commentsLoop(?Token $token, array $comments, array &$response){
        $i = 0;
        foreach($comments as $comment){
            $user = new User();
            $filter = [ "_id" => new ObjectId($comment->getAuthor()) ];
            $user_found = $user->user_get($filter);
            $response['comments'][$i] = [
                //'id' => $comment->getId(),
                //'article' => $comment->getArticle(),
                //'author' => $comment->getAuthor(),
                'author_name' => $user->getUsername(),
                'comment' => $comment->getComment(),
                'cu_comment' => false,
                'creation_time' => $comment->getCrTime(),
                'last_modified' => $comment->getLastMod()
            ];
            if($token != null){
                //Add these properties if user is logged and it's his comment
                $comment_author_id = $comment->getAuthor();
                $logged_user_id = $token->getUserId();
                if($comment_author_id == $logged_user_id){
                    //This comment belong to current logged user
                    $response['comments'][$i]['id'] = $comment->getId();
                    $response['comments'][$i]['cu_comment'] = true;
                }
            }
        }
    }

    protected static function tokenExists(array $headers): ?Token{
        $token = null;
        $token_exists = (isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != '' && $headers[C::KEY_AUTH] != 'undefined');
        if($token_exists){
            //Used to set the editable comments (only logged user comments)
            $token = new Token();
            $filter = ['token_key' => $headers[C::KEY_AUTH]];
            $got_token = $token->token_get($filter);
            if($got_token === false) $token = null;
        }
        return $token;
    }
}

?>