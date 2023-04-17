<?php

//List of comments of single article

require_once("../../../../../vendor/autoload.php");

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\CommentList;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;

$response = [
    C::KEY_MESSAGE => '',
    C::KEY_DONE => false,
    'comments' => [],
    C::KEY_EMPTY => false,
    'error' => false
];
$headers = getallheaders();

if(isset($_GET['permalink']) && $_GET['permalink'] != '' && $_GET['permalink'] != 'undefined'){
    //file_put_contents(C::FILE_LOG,"article comments GET => ".var_export($_GET,true)."\r\n",FILE_APPEND);
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../");
    $dotenv->safeLoad();
    $permalink = $_GET['permalink'];
    try{
        $token = token_exists($headers);
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
                $response[C::KEY_EMPTY] = true;
                $response[C::KEY_MESSAGE] = C::COMMENTLIST_EMPTY;
            }
            $response[C::KEY_DONE] = true;
            http_response_code(200);
        }//if($article_found){
        else{
            http_response_code(500);
            $response['error'] = true;
            $response[C::KEY_MESSAGE] = C::COMMENTLIST_ERROR;
        }  
    }catch(Exception $e){
        http_response_code(500);
        $message = $e->getMessage();
        file_put_contents(C::FILE_LOG,"Exception message => ".var_export($message,true)."\r\n",FILE_APPEND);
        $response['error'] = true;
        $response[C::KEY_MESSAGE] = C::COMMENTLIST_ERROR;
    }
}//if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    
echo json_encode($response);

//Check if user is logged
function token_exists(array $headers): ?Token{
    $token = null;
    $token_exists = (isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != '' && $headers[C::KEY_AUTH] != 'undefined');
    if($token_exists){
        //Used to set the editable comments (only logged user comments)
        $token = new Token();
        $filter = ['token_key' => $headers[C::KEY_AUTH]];
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