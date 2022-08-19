<?php

//List of comments of single article

require_once("../../cors.php");
require_once("../../../../../config.php");
require_once("../../config.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/models_errors.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/user_errors.php");
require_once("../../interfaces/article/article_errors.php");
require_once("../../interfaces/article/comment/comment_errors.php");
require_once("../../interfaces/article/comment/commentlist_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/models.php");
require_once("../../classes/token.php");
require_once("../../classes/user.php");
require_once("../../classes/article/article.php");
require_once("../../classes/article/comment/comment.php");
require_once("../../classes/article/comment/commentlist.php");

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\CommentList;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use MongoDB\BSON\ObjectId;

$response = [
    'msg' => '',
    'done' => false,
    'comments' => [],
    'empty' => false,
    'error' => false
];

if(isset($_GET['permalink']) && $_GET['permalink'] != '' && $_GET['permalink'] != 'undefined'){
    file_put_contents(C::FILE_LOG,"article comments GET => ".var_export($_GET,true)."\r\n",FILE_APPEND);
    $permalink = $_GET['permalink'];
    try{
        $token = token_exists($_GET);
        $article = new Article();
        $filter = [
            'permalink' => $permalink
        ];
        $article_found = $article->article_get($filter);
        if($article_found){
            $article_id = $article->getId();
            //file_put_contents(C::FILE_LOG,"Article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
            //Found the article with the given permalink
            $cl = new CommentList();
            $filter = [
                "article" => new ObjectId($article_id)
            ];
            $comments_found = $cl->commentlist_get($filter);
            //file_put_contents(C::FILE_LOG,"comments_found => ".var_export($comments_found,true)."\r\n",FILE_APPEND);
            if($comments_found){
                //At least one comment found
                $comments = $cl->getResults();
                comments_loop($token,$comments,$response);
            }//if($comments_found){
            else{
                $response['empty'] = true;
                $response['msg'] = C::COMMENTLIST_EMPTY;
            }
            $response['done'] = true;
        }//if($article_found){
        else{
            $response['error'] = true;
            $response['msg'] = C::COMMENTLIST_ERROR;
        }  
    }catch(Exception $e){
        $message = $e->getMessage();
        file_put_contents(C::FILE_LOG,"Exception message => ".var_export($message,true)."\r\n",FILE_APPEND);
        $response['error'] = true;
        $response['msg'] = C::COMMENTLIST_ERROR;
    }
}//if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);

//Check if user is logged
function token_exists(array $get): ?Token{
    $token = null;
    $token_exists = (isset($get['token_key']) && $get['token_key'] != '' && $get['token_key'] != 'undefined');
    if($token_exists){
        //Used to set the editable comments (only logged user comments)
        $token = new Token();
        $filter = ['token_key' => $get['token_key']];
        $got_token = $token->token_get($filter);
        if($got_token === false)
            $token = null;
    }
    file_put_contents(C::FILE_LOG,"token_exists token => ".var_export($token,true)."\r\n",FILE_APPEND);
    return $token;
}

//Get the comments of the article and set them to response array
function comments_loop(?Token $token, array $comments, array &$response){
    $i = 0;
    foreach($comments as $comment){
        $user = new User();
        $filter = [
            "_id" => new ObjectId($comment->getAuthor())
        ];
        $user_found = $user->user_get($filter);
        $response['comments'][$i] = [
            //'id' => $comment->getId(),
            //'article' => $comment->getArticle(),
            //'author' => $comment->getAuthor(),
            'author_name' => $user->getUsername(),
            'comment' => $comment->getComment(),
            'cu_comment' => false,
            'creation_time' => $comment->getCrTime(),
            'last_modified' => $comment->getLastMod()
        ];
        if($token !== null){
            //Add these properties if user is logged and it's his comment
            $comment_author_id = $comment->getAuthor();
            file_put_contents(C::FILE_LOG,"comment author id => ".var_export($comment_author_id,true)."\r\n",FILE_APPEND);
            $logged_user_id = $token->getUserId();
            file_put_contents(C::FILE_LOG,"logged user id => ".var_export($logged_user_id,true)."\r\n",FILE_APPEND);
            if($comment_author_id == $logged_user_id){
                //This comment belong to current logged user
                $response['comments'][$i]['id'] = $comment->getId();
                $response['comments'][$i]['cu_comment'] = true;
            }//if($comment_author_id == $logged_user_id){
        }//if($got_token !== null){
        $i++;
    }//foreach($comments as $comment){
}
?>