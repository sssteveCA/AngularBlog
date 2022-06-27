<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../../../interfaces/article/articleauthorizedview_errors.php");
require_once("../../../interfaces/myarticles/deletecontroller_errors.php");
require_once("../../../interfaces/myarticles/deleteview_errors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/article/articleauthorizedcontroller.php");
require_once("../../../classes/article/articleauthorizedview.php");
require_once("../../../classes/myarticles/deletecontroller.php");
require_once("../../../classes/myarticles/deleteview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Myarticles\DeleteController;
use AngularBlog\Classes\Myarticles\DeleteView;

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = [
    'done' => false,
    'msg' => '',
    'post' => $post
];

if(isset($post['article_id'],$post['token_key']) && $post['article_id'] != '' && $post['token_key'] != '' ){
    $token_data = ['token_key' => $post['token_key']];
    $article_data = ['id' => $post['article_id']];
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
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::ARTICLEDELETE_ERROR;
    }
}//if(isset($post['article_id'],$post['token_key']) && $post['article_id'] != '' && $post['token_key'] != '' ){
else{
    $response['msg'] = C::ARTICLEDELETE_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>