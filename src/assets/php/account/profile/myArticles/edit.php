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

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = array(
    'edited' => false,
    'msg' => '',
    'post' => $post
);

if(isset($post['article'],$post['token_key'],$post['article_id']) && $post['token_key'] != '' && $post['article_id'] != ''){
    if(isset($post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article']['title'] != '' && $post['article']['introtext'] != '' && $post['article']['content'] != '' && $post['article']['permalink'] != ''){
        $token_key = $post['token_key'];
        $article_id = $post['article_id'];
        try{

        }catch(Exception $e){
            file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
            $response['msg'] = C::ARTICLEEDITING_ERROR;
        }
    }//if(isset($post['article']['title'],$post['article']['introtext'],$post['article']['content'],$post['article']['permalink'],$post['article']['categories'],$post['article']['tags']) && $post['article'] != '' && $post['article'] != '' && $post['article'] != '' && $post['article'] != '' && $post['article'] != '' && $post['article'] != ''){
    else{
        $response['msg'] = C::FILL_ALL_FIELDS;
    }
}//if(isset($post['article'],$post['token_key'],$post['article_id']) && $post['token_key'] != '' && $post['article_id'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>