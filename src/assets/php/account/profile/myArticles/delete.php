<?php

require_once("../../../../../../vendor/autoload.php");


use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\DeleteController;
use AngularBlog\Classes\Myarticles\DeleteView;
use Dotenv\Dotenv;

$input = file_get_contents('php://input');
$delete = json_decode($input,true);

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => '' //'delete' => $delete
];
$headers = getallheaders();

if(isset($delete['article_id'],$headers[C::KEY_AUTH]) && $delete['article_id'] != '' && $headers[C::KEY_AUTH] != '' ){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ['token_key' => $headers[C::KEY_AUTH]];
    $article_data = ['id' => $delete['article_id']];
    try{
        $token = new Token($token_data);
        $article = new Article($article_data);
        $dc_data = [
            'article' => $article,
            'token' => $token
        ];
        $deleteController = new DeleteController($dc_data);
        $deleteView = new DeleteView($deleteController);
        $response[C::KEY_MESSAGE] = $deleteView->getMessage();
        if($deleteView->isDone())
            $response[C::KEY_DONE] = true;
        else{
            $errnoT = $deleteController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response[C::KEY_EXPIRED] = true;
            }
        }
        http_response_code($deleteView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::ARTICLEDELETE_ERROR;
    }
}//if(isset($delete['article_id'],$headers[C::KEY_AUTH]) && $delete['article_id'] != '' && $headers[C::KEY_AUTH] != '' ){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::ARTICLEDELETE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>