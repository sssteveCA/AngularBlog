<?php

require_once("../../cors.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedcontroller_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedview_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../traits/authorized.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/comment/commentauthorizedcontroller.php");
require_once("../../classes/article/comment/commentauthorizedview.php");

use AngularBlog\Classes\Article\Comment\DeleteController;
use AngularBlog\Classes\Article\Comment\DeleteView;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Comment\Comment;

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

$response = [
    'done' => false,
    'expired' => false,
    'msg' => '',
    'delete' => $delete
];

if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){
    $token_data = ['token_key' => $delete['token_key']];
    $comment_data = ['id' => $delete['comment_id']];
    try{
        $token = new Token($token_data);
        $comment = new Comment($comment_data);
        $dc_data = [
            'comment' => $comment,
            'token' => $token
        ];
        $deleteController = new DeleteController($dc_data);
        $deleteView = new DeleteView($deleteController);
        $response['msg'] = $deleteView->getMessage();
        if($deleteView->isDone())
            $response['done'] = true;
        $errnoT = $deleteController->getToken()->getErrno();
        if($errnoT == Te::TOKENEXPIRED){
            $response['expired'] = true;
        }
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::COMMENTDELETE_ERROR;
    }
}//if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){
else
    //$response['msg'] = C::FILL_ALL_FIELDS;
    $response['msg'] = C::COMMENTDELETE_ERROR;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>