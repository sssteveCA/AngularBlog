<?php

require_once("../cors.php");

require_once("../interfaces/constants.php");
require_once("../interfaces/exception_messages.php");
require_once("../interfaces/from_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/token_errors.php");
require_once("../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../interfaces/article/articleauthorizedview_errors.php");
require_once("../../../../vendor/autoload.php");
require_once("../traits/authorized.trait.php");
require_once("../traits/error.trait.php");
require_once("../traits/message.trait.php");
require_once("../traits/response.trait.php");
require_once("../classes/model.php");
require_once("../classes/article/article.php");
require_once("../classes/token.php");
require_once("../classes/article/articleauthorizedcontroller.php");
require_once("../classes/article/articleauthorizedview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\ArticleAuthorizedView;
use AngularBlog\Classes\Token;
use Dotenv\Dotenv;

$response = array(
    'authorized' => false,
    'article' => [],
    C::KEY_MESSAGE => ''
);

$input = file_get_contents("php://input");
$post = json_decode($input,true);

if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $token_key = $post['token_key'];
    $article_id = $post['article_id'];
    //file_put_contents(C::FILE_LOG,"article_authorized article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
    try{
        $article = new Article(['id' => $article_id]);
        $token = new Token(['token_key' => $token_key]);
        $data = [
            'article' => $article,
            'token' => $token
        ];
        $aac = new ArticleAuthorizedController($data);
        $aav = new ArticleAuthorizedView($aac);
        $response[C::KEY_MESSAGE] = $aav->getMessage();
        if($response[C::KEY_MESSAGE] != Aace::ARTICLE_NOTFOUND_MSG)
            //Article found with passed id
        if($aav->isDone()){
            $response['authorized'] = true;
            $article = $aav->getController()->getArticle();
            $response['article'] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'introtext' => $article->getIntrotext(),
                'content' => $article->getContent(),
                'permalink' => $article->getPermalink(),
                'categories' => implode(",",$article->getCategories()),
                'tags' => implode(",",$article->getTags())
            ];
        }
        http_response_code($aav->getResponseCode());  
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::ERROR_UNKNOWN;
    }
}//if(isset($post['token_key'],$post['username'],$post['article_id']) && $post['token_key'] != '' && $post['username'] != '' && $post['article_id'] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>