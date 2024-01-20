<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\DeleteController;
use AngularBlog\Classes\Myarticles\DeleteView;
use Dotenv\Dotenv;

/**
 * JSON response for delete article DELETE route
 */
class DeleteArticle{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        $headers = $params['headers'];
        if(isset($delete['article_id'],$headers[C::KEY_AUTH]) && $delete['article_id'] != '' && $headers[C::KEY_AUTH] != '' ){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $token = new Token($token_data);
                $article = new Article($article_data);
                $dc_data = [ 'article' => $article,'token' => $token ];
                $deleteController = new DeleteController($dc_data);
                $deleteView = new DeleteView($deleteController);
                $response[C::KEY_MESSAGE] = $deleteView->getMessage();
                if($deleteView->isDone())
                    $response[C::KEY_DONE] = true;
                else{
                    $errnoT = $deleteController->getToken()->getErrno();
                    if($errnoT == Te::TOKENEXPIRED) $response[C::KEY_EXPIRED] = true;
                }
                $response[C::KEY_CODE] = $deleteView->getResponseCode();
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::ARTICLEDELETE_ERROR;
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::ARTICLEDELETE_ERROR;
        }
        return $response;
    }
}

?>