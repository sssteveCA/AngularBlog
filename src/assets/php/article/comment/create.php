<?php

require_once("../../cors.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/article_errors.php");
require_once("../../interfaces/article/comment/addcommentcontroller_errors.php");
require_once("../../interfaces/article/comment/addcommentview_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/article.php");
require_once("../../classes/article/comment/addcommentcontroller.php");
require_once("../../classes/article/comment/addcommentview.php");

use AngularBlog\Interfaces\Article\Comment\AddCommentControllerErrors as Acce;
use AngularBlog\Interfaces\Article\Comment\AddCommentViewErrors as Acve;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\Comment\AddCommentController;
use AngularBlog\Classes\Article\Comment\AddCommentView;
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
        $addCommentController = new AddCommentController($data);
        $addCommentView = new AddCommentView($addCommentController);
        if($addCommentView->isDone())
            $response['done'] = true;
        else{
            $msg = $addCommentView->getMessage();
        }

    }catch(Exception $e){
        $msg = $e->getMessage();
        file_put_contents(C::FILE_LOG,"create exception => ".var_export($msg,true)."\r\n",FILE_APPEND);
        switch($msg){
            case Acce::NOARTICLEPERMALINK_EXC:
            case Acce::NOCOMMENT_EXC:
            case Acce::NOTOKENKEY_EXC:
            case Acve::NOADDCOMMENTCONTROLLERINSTANCE_EXC:
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