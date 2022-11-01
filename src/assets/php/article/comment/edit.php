<?php

require_once("../../cors.php");
require_once("../../../../../config.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/exception_messages.php");
require_once("../../interfaces/from_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedcontroller_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedview_errors.php");
require_once("../../interfaces/article/comment/editcontroller_errors.php");
require_once("../../interfaces/article/comment/editview_errors.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../traits/authorized.trait.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../../../../vendor/autoload.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/comment/commentauthorizedcontroller.php");
require_once("../../classes/article/comment/commentauthorizedview.php");
require_once("../../classes/article/comment/editcontroller.php");
require_once("../../classes/article/comment/editview.php");

use AngularBlog\Classes\Article\Comment\EditController;
use AngularBlog\Classes\Article\Comment\EditView;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;

$input = file_get_contents("php://input");
$patch = json_decode($input, true);

$response = [
    'done' => false,
    'expired' => false,
    'msg' => ''
    //'patch' => $patch
];

if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$patch['token_key']) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $patch['token_key'] != ''){
    $token_data = ['token_key' => $patch['token_key']];
    $comment_data = [
        'id' => $patch['comment_id'],
        'comment' => $patch['new_comment']
    ];
    try{
        $token = new Token($token_data);
        $comment = new Comment($comment_data);
        $ec_data = [
            'token' => $token,
            'comment' => $comment
        ];
        $editController = new EditController($ec_data);
        $editView = new EditView($editController);
        $response['msg'] = $editView->getMessage();
        if($editView->isDone()){
            $response['done'] = true;
            $response['comment'] = $comment->getComment();
        } 
        else{
            $errnoT = $editController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response['expired'] = true;
            }
        }
    }catch(Exception $e){
        //file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::COMMENTUPDATE_ERROR;
    }
}//if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$patch['token_key']) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $patch['token_key'] != ''){
else{
    $response['msg'] = C::COMMENTUPDATE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>