<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;
use Exception;

/**
 * JSON response for single article GET route
 */
class GetArticle{

    public static function content(array $params): array{
        $response = [
           C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_MESSAGE => ''
        ];
        $get = $params['get'];
        if(isset($get['permalink']) && $get['permalink'] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $permalink = $get['permalink'];
                $filter = ['permalink' => $permalink];
                $article = new Article([]);
                $got = $article->article_get($filter);
                if($got){
                    //Article with given permalink found
                    $authorId = $article->getAuthor();
                    $response[C::KEY_DATA] = [
                        'id' => $article->getId(),
                        'title' => $article->getTitle(),
                        'authorId' => $authorId,
                        'permalink' => $article->getPermalink(),
                        'content' => $article->getContent(),
                        'introtext' => $article->getIntrotext(),
                        'categories' => implode(",",$article->getCategories()),
                        'tags' => implode(",",$article->getTags()),
                        'creation_time' => $article->getCrTime(),
                        'last_modified' => $article->getLastMod()
                    ];
                    $user = new User([]);
                    $filter = ['_id' => new ObjectId($authorId)];
                    $userGot = $user->user_get($filter);
                    if($userGot){
                        //User getted by author id field of article collection
                        $response[C::KEY_DATA]['author'] =  $user->getUsername();
                    }
                    else $response[C::KEY_DATA]['author'] = 'Sconosciuto';
                    $response[C::KEY_DONE] = true;
                }
                else{
                    $response[C::KEY_CODE] = 404;
                    $response[C::KEY_MESSAGE] = "Articolo non trovato";
                    $response['notfound'] = true;
                }
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::ARTICLEVIEW_ERROR;
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