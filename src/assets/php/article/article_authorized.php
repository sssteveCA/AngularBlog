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
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Interfaces\Article\ArticleAuthorizedViewErrors as Aave;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\ArticleAuthorizedView;
use AngularBlog\Classes\Token;

$response = array(
    'authorized' => false,
    'msg' => ''
);

$input = file_get_contents("php://input");
$post = json_decode($input,true);

if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
    $token_key = $post['token_key'];
    $article_id = $post['article_id'];
    file_put_contents(C::FILE_LOG,"article_authorized article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
    try{
        $article = new Article(['id' => $article_id]);
        $token = new Token(['token_key' => $token_key]);
        $data = [
            'article' => $article,
            'token' => $token
        ];
        $aac = new ArticleAuthorizedController($data);
        $aav = new ArticleAuthorizedView($aac);
        $response['msg'] = $aav->getMessage();
        if($response['msg'] != Aace::ARTICLE_NOTFOUND_MSG)
            //Article found with passed id
        if($aav->isDone())
            $response['authorized'] = true;
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response['msg'] = C::ARTICLEEDITING_ERROR;
    }
}//if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>