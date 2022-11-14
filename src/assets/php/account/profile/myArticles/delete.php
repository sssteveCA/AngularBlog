<?php

require_once("../../../cors.php");
require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../../../interfaces/article/articleauthorizedview_errors.php");
require_once("../../../interfaces/myarticles/deletecontroller_errors.php");
require_once("../../../interfaces/myarticles/deleteview_errors.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../traits/authorized.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/article/articleauthorizedcontroller.php");
require_once("../../../classes/article/articleauthorizedview.php");
require_once("../../../classes/myarticles/deletecontroller.php");
require_once("../../../classes/myarticles/deleteview.php");

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
    'done' => false,
    'expired' => false,
    'msg' => ''
    //'delete' => $delete
];

if(isset($delete['article_id'],$delete['token_key']) && $delete['article_id'] != '' && $delete['token_key'] != '' ){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
    $token_data = ['token_key' => $delete['token_key']];
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
        $response['msg'] = $deleteView->getMessage();
        if($deleteView->isDone())
            $response['done'] = true;
        else{
            $errnoT = $deleteController->getToken()->getErrno();
            if($errnoT == Te::TOKENEXPIRED){
                $response['expired'] = true;
            }
        }
        http_response_code($deleteView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::ARTICLEDELETE_ERROR;
    }
}//if(isset($delete['article_id'],$delete['token_key']) && $delete['article_id'] != '' && $delete['token_key'] != '' ){
else{
    http_response_code(400);
    $response['msg'] = C::ARTICLEDELETE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>