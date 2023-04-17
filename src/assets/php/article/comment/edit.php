<?php

require_once("../../../../../vendor/autoload.php");

use AngularBlog\Classes\Article\Comment\EditController;
use AngularBlog\Classes\Article\Comment\EditView;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$input = file_get_contents("php://input");
$patch = json_decode($input, true);

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => '' //'patch' => $patch
];
$headers = getallheaders();

if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$headers[C::KEY_AUTH]) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $headers[C::KEY_AUTH] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
    $token_data = ['token_key' => $headers[C::KEY_AUTH]];
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
        $response[C::KEY_MESSAGE] = $editView->getMessage();
        if($editView->isDone()){
            $response[C::KEY_DONE] = true;
            $response['comment'] = $comment->getComment();
        } 
        else{
            $errnoT = $editController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response[C::KEY_EXPIRED] = true;
            }
        }
        http_response_code($editView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        //file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::COMMENTUPDATE_ERROR;
    }
}//if(isset($patch['comment_id'],$patch['new_comment'],$patch['old_comment'],$headers[C::KEY_AUTH]) && $patch['comment_id'] != '' && $patch['new_comment'] != '' && $patch['old_comment'] != '' && $headers[C::KEY_AUTH] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::COMMENTUPDATE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>