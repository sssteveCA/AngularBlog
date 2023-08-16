<?php

require_once("../../../../../vendor/autoload.php");

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
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => '', //'delete' => $delete
];
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH],$delete['comment_id']) && $headers[C::KEY_AUTH] != '' && $delete['comment_id'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
    $token_data = ['token_key' => $headers[C::KEY_AUTH]];
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
        $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
    }
}//if(isset($headers[C::KEY_AUTH],$delete['comment_id']) && $headers[C::KEY_AUTH] != '' && $delete['comment_id'] != ''){
else{
    http_response_code(400);
    //$response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
    $response[C::KEY_MESSAGE] = C::COMMENTDELETE_ERROR;
}
    
echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>