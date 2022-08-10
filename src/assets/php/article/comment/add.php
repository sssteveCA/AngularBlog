<?php

require_once("../../cors.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/article/article_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/token.php");

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array(
    'done' => false,
    'expired' => false,
    'msg' => ''
);

if(isset($post['permalink'],$post['token_key'],$post['comment']) && $post['permalink'] != '' && $post['token_key'] != '' && $post['comment'] != ''){
    try{
        $data = [
            'token_key' => $post['token_key'],
            'comment_text' => $post['comment_text'],
            'permalink' => $post['permalink']
        ];

    }catch(Exception $e){

    }
}//if(isset($post['permalink'],$post['token_key'],$post['comment'])){
else{
    $response['msg'] = C::INSERTCOMMENT_ERROR;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>