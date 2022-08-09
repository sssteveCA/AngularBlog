<?php

//List of comments of single article

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/comment/comment_errors.php");
require_once("../interfaces/comment/commentlist_errors.php");
require_once("../vendor/autoload.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/models.php");
require_once("../classes/article/article.php");
require_once("../classes/comment/comment.php");
require_once("../classes/comment/commentlist.php");

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\CommentList;
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
            //Found the article with the given permalink
            $cl = new CommentList();
            $filter = [
                "_id" => new ObjectId($article->getId())
            ];
            $comments_found = $cl->commentlist_get($filter);
            if($comments_found){
                //At least one comment found
                $comments = $cl->getResults();
                foreach($comments as $comment){
                    $response['comments'][] = [
                        'id' => $comment->getId(),
                        'article' => $comment->getArticle(),
                        'author' => $comment->getAuthor(),
                        'comment' => $comment->getComment(),
                        'creation_time' => $comment->getCrTime(),
                        'last_modified' => $comment->getLastMod()
                    ];
                }//foreach($comments as $comment){
                $response['done'] = true;
            }//if($comments_found){
            else{
                $response['empty'] = true;
                $response['msg'] = C::COMMENTLIST_EMPTY;
            }
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
?>