<?php

require_once("../../cors.php");
//require_once("../../../../../config.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/exception_messages.php");
require_once("../../interfaces/from_errors.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedcontroller_errors.php");
require_once("../../interfaces/article/comment/commentauthorizedview_errors.php");
require_once("../../interfaces/article/comment/deletecontroller_errors.php");
require_once("../../interfaces/article/comment/deleteview_errors.php");
require_once("../../../../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../traits/message.trait.php");
require_once("../../traits/response.trait.php");
require_once("../../traits/authorized.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/comment/commentauthorizedcontroller.php");
require_once("../../classes/article/comment/commentauthorizedview.php");
require_once("../../classes/article/comment/deletecontroller.php");
require_once("../../classes/article/comment/deleteview.php");

use AngularBlog\Classes\Article\Comment\DeleteController;
use AngularBlog\Classes\Article\Comment\DeleteView;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Comment\Comment;
use Dotenv\Dotenv;

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

$response = [
    C::KEY_DONE => false,
    C::KEY_EXPIRED => false,
    C::KEY_MESSAGE => '',
    //'delete' => $delete
];

if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
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
        $response[C::KEY_MESSAGE] = $deleteView->getMessage();
        if($deleteView->isDone())
            $response[C::KEY_DONE] = true;
        $errnoT = $deleteController->getToken()->getErrno();
        if($errnoT == Te::TOKENEXPIRED){
            $response[C::KEY_EXPIRED] = true;
        }
        http_response_code($deleteView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
    }
}//if(isset($delete['token_key'],$delete['comment_id']) && $delete['token_key'] != '' && $delete['comment_id'] != ''){
else{
    http_response_code(400);
    //$response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
    $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>