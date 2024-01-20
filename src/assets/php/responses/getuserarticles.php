<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Myarticles\GetController;
use AngularBlog\Interfaces\MyArticles\GetControllerErrors as Gce;
use AngularBlog\Classes\Myarticles\GetView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for get user articles list GET route
 */
class GetUserArticles{
    
    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_MESSAGE => '', 'articles' => []
        ];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $data = [ 'token_key' => $headers[C::KEY_AUTH] ];
                $getController = new GetController($data);
                $getView = new GetView($getController);
                if($getView->articlesFound()){
                    //At least one article found
                    $articles = $getController->getArticleList()->getResults();
                    foreach($articles as $article){
                        $response['articles'][] = array(
                            'id' => $article->getId(),
                            'title' => $article->getTitle(),
                            'author' => $article->getAuthor(),
                            'permalink' => $article->getPermalink(),
                            'content' => $article->getContent(),
                            'introtext' => $article->getIntrotext(),
                            'categories' => implode(",",$article->getCategories()),
                            'tags' => implode(",",$article->getTags()),
                            'creation_time' => $article->getCrTime(),
                            'last_modified' => $article->getLastMod()
                        );
                    }//foreach($articles as $article){
                    $response[C::KEY_DONE] = true;
                }
                else{
                    $response[C::KEY_MESSAGE] = $getView->getMessage();
                    if($response[C::KEY_MESSAGE] == Gce::NOARTICLESFOUND_MSG) {
                        $response[C::KEY_EMPTY] = true;
                        $response[C::KEY_DONE] = true;
                    }
                }
                $response[C::KEY_CODE] = $getView->getResponseCode();
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