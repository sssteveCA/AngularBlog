<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Article\Comment\AddControllerErrors as Ace;
use AngularBlog\Interfaces\Article\Comment\AddViewErrors as Ave;
use AngularBlog\Classes\Article\Comment\AddController;
use AngularBlog\Classes\Article\Comment\AddView;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for create comment POST route
 */
class CreateComment{

    public static function content(array $params): array{
        $response = [
           C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
        ];
        $headers = $params['headers'];
        $post = $params['post'];
        if(isset($post['permalink'],$headers[C::KEY_AUTH],$post['comment_text']) && $post['permalink'] != '' && $headers[C::KEY_AUTH] != '' && $post['comment_text'] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $data = [
                    'token_key' => $headers[C::KEY_AUTH],
                    'comment_text' => $post['comment_text'],
                    'permalink' => $post['permalink']
                ];
                $addController = new AddController($data);
                $addCommentView = new AddView($addController);
                if($addCommentView->isDone()) $response[C::KEY_DONE] = true;
                else $response[C::KEY_MESSAGE] = $addCommentView->getMessage();
                $response[C::KEY_CODE] = $addCommentView->getResponseCode();
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $msg = $e->getMessage();
                switch($msg){
                    case Ace::NOARTICLEPERMALINK_EXC:
                    case Ace::NOCOMMENT_EXC:
                    case Ace::NOTOKENKEY_EXC:
                    case Ave::NOADDCOMMENTCONTROLLERINSTANCE_EXC:
                    default:     
                        $response[C::KEY_MESSAGE] = C::COMMENTCREATION_ERROR;
                        break;
                }
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::INSERTCOMMENT_ERROR;
        }
        return $response;
    }
}

?>