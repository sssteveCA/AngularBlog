<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../interfaces/article/articleauthorizedview_errors.php");
require_once("../vendor/autoload.php");
require_once("../classes/model.php");
require_once("../classes/article/article.php");
require_once("../classes/token.php");
require_once("../classes/article/articleauthorizedcontroller.php");
require_once("../classes/article/articleauthorizedview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Interfaces\Article\ArticleAuthorizedViewErrors;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\ArticleAuthorizedView;
use AngularBlog\Classes\Token;

$response = array(
    'done' => false,
    'msg' => ''
);

$input = file_get_contents("php://input");
$post = json_decode($input,true);

if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
    $token_key = $post['token_key'];
    $article_id = $post['article_id'];
    try{
        $article = new Article(['_id' => $article_id]);
        $token = new Token(['token_key' => $token_key]);
        $data = [
            'article' => $article,
            'token' => $token
        ];
        $aac = new ArticleAuthorizedController($data);
        $aav = new ArticleAuthorizedView($aac);
        $response['msg'] = $aav->getMessage();
        if($aav->isDone())
            $response['done'] = true;
    }catch(Exception $e){
        $response['msg'] = C::ARTICLEEDITING_ERROR;
    }
}//if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>