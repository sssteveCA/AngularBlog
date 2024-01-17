<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;
use Exception;

/**
 * JSON response for last post GET route
 */
class LastPosts{

    public function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DATA => [], C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_MESSAGE => ""
        ];
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
            $dotenv->load();
            $al = new ArticleList();
            $options = [
                'limit' => 10,
                'sort' => ['last_modified' => -1]
            ];
            $found = $al->articlelist_get([],$options);
            if($found){
                $articles = $al->getResults();
                $i = 0;
                foreach($articles as $article){
                    $response[C::KEY_DATA][$i] = array(
                        'categories' => implode(",", $article->getCategories()),
                        'introtext' => $article->getIntrotext(),
                        'last_modified' => $article->getLastMod(),
                        'permalink' => $article->getPermalink(),
                        'tags' => implode(",", $article->getTags()),
                        'title' => $article->getTitle(),
                    );
                    $user = new User([]);
                    $got = $user->user_get(['_id' => new ObjectId($article->getAuthor())]);
                    if($got) $author = $user->getUsername();
                    else $author = "Autore sconosciuto";
                    $response[C::KEY_DATA][$i]['author'] = $author;
                    $i++;
                }
            }
            else{
                $response[C::KEY_EMPTY] = true;
                $response[C::KEY_MESSAGE] = C::NEWS_EMPTY;
            }
            $response[C::KEY_DONE] = true;
        }catch(Exception $e){
            $response[C::KEY_MESSAGE] = C::NEWS_ERROR;
            $response[C::KEY_CODE] = 500;
        }
        return $response;
    }
}

?>