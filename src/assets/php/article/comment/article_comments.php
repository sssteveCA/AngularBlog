<?php

//List of comments of single article

require_once("../../cors.php");
require_once("../../config.php");
require_once("../../interfaces/constants.php");
require_once("../../interfaces/models_errors.php");
require_once("../../interfaces/model_errors.php");
require_once("../../interfaces/token_errors.php");
require_once("../../interfaces/user_errors.php");
require_once("../../interfaces/article/article_errors.php");
require_once("../../interfaces/comment/comment_errors.php");
require_once("../../interfaces/comment/commentlist_errors.php");
require_once("../../vendor/autoload.php");
require_once("../../traits/error.trait.php");
require_once("../../classes/model.php");
require_once("../../classes/models.php");
require_once("../../classes/token.php");
require_once("../../classes/user.php");
require_once("../../classes/article/article.php");
require_once("../../classes/comment/comment.php");
require_once("../../classes/comment/commentlist.php");

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

if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
    $permalink = $_GET['permalink'];
    try{
        
        $article = new Article();
        $filter = [
            'permalink' => $permalink
        ];
        $article_found = $article->article_get($filter);
        if($article_found){
            $article_id = $article->getId();
            file_put_contents(C::FILE_LOG,"Article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
            //Found the article with the given permalink
            $cl = new CommentList();
            $filter = [
                "article" => new ObjectId($article_id)
            ];
            $comments_found = $cl->commentlist_get($filter);
            file_put_contents(C::FILE_LOG,"comments_found => ".var_export($comments_found,true)."\r\n",FILE_APPEND);
            if($comments_found){
                //At least one comment found
                $comments = $cl->getResults();
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
                        'creation_time' => $comment->getCrTime(),
                        'last_modified' => $comment->getLastMod()
                    ];
                    if($got_token){
                        //Add these properties if user is logged and it's his comment
                        $comment_author_id = $comment->getAuthor();
                        $logged_user_id = $token->getUserId();
                        if($comment_author_id == $logged_user_id){
                            //This comment belong to current logged user
                            $response['comments']['id'] = $comment->getId();
                        }//if($comment_author_id == $logged_user_id){
                    }
                    $i++;
                }//foreach($comments as $comment){
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
        $response['error'] = true;
        $response['msg'] = C::COMMENTLIST_ERROR;
    }
}//if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);

function token_exists(array $post): ?Token{
    $token = null;
    $token_exists = isset($post['token_key']);
    if($token_exists){
        //Used to set the editable comments (only logged user comments)
        $token = new Token();
        $filter = ['token_key' => $post['token_key']];
        $got_token = $token->token_get($filter);
        if($got_token === false)
            $token = null;
    }
    return $token;
}
?>