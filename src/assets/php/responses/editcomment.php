<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Article\Comment\EditController;
use AngularBlog\Classes\Article\Comment\EditView;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

/**
 * JSON response for edit comment PUT route
 */
class EditComment{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        if(isset($put['comment_id'],$put['new_comment'],$put['old_comment'],$headers[C::KEY_AUTH]) && $put['comment_id'] != '' && $put['new_comment'] != '' && $put['old_comment'] != '' && $headers[C::KEY_AUTH] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $token_data = ['token_key' => $headers[C::KEY_AUTH]];
                $comment_data = [
                    'id' => $put['comment_id'], 'comment' => $put['new_comment']
                ];
                $token = new Token($token_data);
                $comment = new Comment($comment_data);
                $ec_data = [
                    'token' => $token, 'comment' => $comment
                ];
                $editController = new EditController($ec_data);
                $editView = new EditView($editController);
                $response[C::KEY_MESSAGE] = $editView->getMessage();
                if($editView->isDone()){
                    $response[C::KEY_DONE] = true;
                    $response['comment'] = $comment->getComment();
                }
                else{
                    $errnoT = $editController->getToken()->getErrno();
                    if($errnoT == Te::TOKENEXPIRED) $response[C::KEY_EXPIRED] = true;
                }
                $response[C::KEY_CODE] = $editView->getResponseCode();
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::COMMENTUPDATE_ERROR;
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::COMMENTUPDATE_ERROR;
        }
        return $response;
    }

}

?>