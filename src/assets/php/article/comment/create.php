<?php

require_once("../../cors.php");
require_once("../../../../../config.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/exception_messages.php");
require_once("../../interfaces/from_errors.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/article_errors.php");
require_once("../../interfaces/article/comment/addcontroller_errors.php");
require_once("../../interfaces/article/comment/addview_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/article.php");
require_once("../../classes/article/comment/addcontroller.php");
require_once("../../classes/article/comment/addview.php");

use AngularBlog\Interfaces\Article\Comment\AddControllerErrors as Ace;
use AngularBlog\Interfaces\Article\Comment\AddViewErrors as Ave;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\Comment\AddController;
use AngularBlog\Classes\Article\Comment\AddtController;
use AngularBlog\Classes\Article\Comment\AddView;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array(
    'done' => false,
    'expired' => false,
    'msg' => ''
);

if(isset($post['permalink'],$post['token_key'],$post['comment_text']) && $post['permalink'] != '' && $post['token_key'] != '' && $post['comment_text'] != ''){
    try{
        $data = [
            'token_key' => $post['token_key'],
            'comment_text' => $post['comment_text'],
            'permalink' => $post['permalink']
        ];
        file_put_contents(C::FILE_LOG,"create data => ".var_export($data,true)."\r\n",FILE_APPEND);
        $addController = new AddController($data);
        $addCommentView = new AddView($addController);
        if($addCommentView->isDone())
            $response['done'] = true;
        else{
            $msg = $addCommentView->getMessage();
        }

    }catch(Exception $e){
        $msg = $e->getMessage();
        file_put_contents(C::FILE_LOG,"create exception => ".var_export($msg,true)."\r\n",FILE_APPEND);
        switch($msg){
            case Ace::NOARTICLEPERMALINK_EXC:
            case Ace::NOCOMMENT_EXC:
            case Ace::NOTOKENKEY_EXC:
            case Ave::NOADDCOMMENTCONTROLLERINSTANCE_EXC:
                $response['msg'] = C::COMMENTCREATION_ERROR;
                break;
            default:
                $response['msg'] = C::ERROR_UNKNOWN;
                break;
        }
    }
}//if(isset($post['permalink'],$post['token_key'],$post['comment'])){
else{
    $response['msg'] = C::INSERTCOMMENT_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>