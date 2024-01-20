<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Classes\Myarticles\EditView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for edit article PUT route
 */
class EditArticle{

    public static function content(array $params): array{
        $response = [
            C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        $headers = $params['headers'];
        $put = $params['put'];
        if(isset($put['article'],$headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
            if(isset($put['article']['id'],$put['article']['title'],$put['article']['introtext'],$put['article']['content'],$put['article']['permalink'],$put['article']['categories'],$put['article']['tags']) && $put['article']['id'] != '' && $put['article']['title'] != '' && $put['article']['introtext'] != '' && $put['article']['content'] != '' && $put['article']['permalink'] != ''){
                try{
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->load();
                    $data = [
                        'token_key' => $headers[C::KEY_AUTH],
                        'article_id' => $put['article']['id']
                    ];
                    $token_data = ['token_key' => $headers[C::KEY_AUTH]];
                    $article_data = [
                        'id' => $post['article']['id'],
                        'title' => $post['article']['title'],
                        'introtext' => $post['article']['introtext'],
                        'content' => $post['article']['content'],
                        'permalink' => $post['article']['permalink'],
                        'categories' => explode(",",$post['article']['categories']),
                        'tags' => explode(",",$post['article']['tags'])
                    ];
                    $token = new Token($token_data);
                    $article = new Article($article_data);
                    $ec_data = [ 'article' => $article, 'token' => $token ];
                    $editController = new EditContoller($ec_data);
                    $editView = new EditView($editController);
                    $response[C::KEY_MESSAGE] = $editView->getMessage();          
                    if($editView->isDone()) $response[C::KEY_DONE] = true;
                    else{
                        $errnoT = $editController->getToken()->getErrno();
                        if($errnoT == Te::TOKENEXPIRED){
                            $response[C::KEY_EXPIRED] = true;
                        }
                    }
                    $response[C::KEY_CODE] = $editView->getResponseCode();
                }
                catch(Exception $e){
                    $response[C::KEY_CODE] = 500;
                    $response[C::KEY_MESSAGE] = C::ARTICLEEDITING_ERROR;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::ARTICLEEDITING_ERROR;
        }
        return $response;
    }
}

?>