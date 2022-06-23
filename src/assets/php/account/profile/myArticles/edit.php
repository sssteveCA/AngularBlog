<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/article/articleauthorizedcontroller_errors.php");
require_once("../../../interfaces/article/articleauthorizedview_errors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/article/articleauthorizedcontroller.php");
require_once("../../../classes/article/articleauthorizedview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Classes\Article\ArticleAuthorizedView;

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = array(
    'edited' => false,
    'msg' => '',
    'post' => $post
);

if(isset($post['article'],$post['token_key']) && $post['token_key'] != ''){
    if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
        $data = [
            'token_key' => $post['token_key'],
            'article_id' => $post['article']['id']
        ];
        $status = control($data); 
        if($status['authorized'] === true){
            //User can edit this article
            $response['msg'] = 'Authorized';
        }//if($status['authorized'] === true){
        else{
            $response['msg'] = auth_error_message($status['msg']);
        }
    }//if(isset($post['article']['id'],$post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['id'] != '' && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
    else{
        $response['msg'] = C::FILL_ALL_FIELDS;
    }
}//if(isset($post['article'],$post['token_key']) && $post['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

//Check if user is authorized to edit the data
function control(array $data): array{
    $status = [
        'authorized' => false,
        'msg' => ''
    ];
    try{
        $article = new Article(['id' => $data['article_id']]);
        $token = new Token(['token_key' => $data['token_key']]);
        $data = [
            'article' => $article,
            'token' => $token
        ];
        $aac = new ArticleAuthorizedController($data);
        $aav = new ArticleAuthorizedView($aac);
        $status['msg'] = $aav->getMessage();
        if($aav->isDone()){
            $status['authorized'] = true;
        }
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
        $status['msg'] = C::ARTICLEEDITING_ERROR;
    }
    return $status;
}

//Display an authorization error message that show the status of operation
function auth_error_message(string $controller_msg): string{
    $message = '';
    file_put_contents(C::FILE_LOG,var_export($controller_msg,true)."\r\n",FILE_APPEND);
    switch($controller_msg){
        case Aace::TOKEN_NOTFOUND_MSG:
        case Aace::FORBIDDEN_MSG:
            $message = Aace::FORBIDDEN_MSG;
            break;
        default:
            $message = C::ARTICLEEDITING_ERROR;
            break;
    }
    return $message;
}
?>