<?php

require_once("../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Article\Comment\AddControllerErrors as Ace;
use AngularBlog\Interfaces\Article\Comment\AddViewErrors as Ave;
use AngularBlog\Classes\Article\Comment\AddController;
use AngularBlog\Classes\Article\Comment\AddView;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array(
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ''
);
$headers = getallheaders();


if(isset($post['permalink'],$headers[C::KEY_AUTH],$post['comment_text']) && $post['permalink'] != '' && $headers[C::KEY_AUTH] != '' && $post['comment_text'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
    try{
        $data = [
            'token_key' => $headers[C::KEY_AUTH],
            'comment_text' => $post['comment_text'],
            'permalink' => $post['permalink']
        ];
        file_put_contents(C::FILE_LOG,"create data => ".var_export($data,true)."\r\n",FILE_APPEND);
        $addController = new AddController($data);
        $addCommentView = new AddView($addController);
        if($addCommentView->isDone())
            $response[C::KEY_DONE] = true;
        else{
            $msg = $addCommentView->getMessage();
        }
        http_response_code($addCommentView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $msg = $e->getMessage();
        file_put_contents(C::FILE_LOG,"create exception => ".var_export($msg,true)."\r\n",FILE_APPEND);
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
}//if(isset($post['permalink'],$headers[C::KEY_AUTH],$post['comment'])){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::INSERTCOMMENT_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>