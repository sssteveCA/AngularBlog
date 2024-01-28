<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\Comment\DeleteController;
use AngularBlog\Classes\Article\Comment\DeleteView;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Comment\Comment;
use Dotenv\Dotenv;

/**
 * JSON response for delete comment DELETE route
 */
class DeleteComment{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        if(isset($headers[C::KEY_AUTH],$delete['comment_id']) && $headers[C::KEY_AUTH] != '' && $delete['comment_id'] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $token_data = ['token_key' => $headers[C::KEY_AUTH]];
                $comment_data = ['id' => $delete['comment_id']];
                $token = new Token($token_data);
                $comment = new Comment($comment_data);
                $dc_data = [
                    'comment' => $comment,'token' => $token
                ];
                $deleteController = new DeleteController($dc_data);
                $deleteView = new DeleteView($deleteController);
                $response[C::KEY_MESSAGE] = $deleteView->getMessage();
                if($deleteView->isDone()) $response[C::KEY_DONE] = true;
                $errnoT = $deleteController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED) $response[C::KEY_EXPIRED] = true;
                $response[C::KEY_CODE] = $deleteView->getResponseCode();
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
        }
        return $response;
    }
}

?>