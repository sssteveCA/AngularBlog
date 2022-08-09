<?php

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/user_errors.php");
require_once("../vendor/autoload.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/article/article.php");
require_once("../classes/user.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\User;
use MongoDB\BSON\ObjectId;

$response = array();
$response['msg'] = '';
$response['done'] = false; 
$params = array();

if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
    $permalink = $_GET['permalink'];
    $filter = ['permalink' => $permalink];
    try{
        $article = new Article([]);
        $got = $article->article_get($filter);
        if($got){
            //Article with given permalink found
            $authorId = $article->getAuthor();
            $response['article'] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'authorId' => $authorId,
                'permalink' => $article->getPermalink(),
                'content' => $article->getContent(),
                'introtext' => $article->getIntrotext(),
                'categories' => implode(",",$article->getCategories()),
                'tags' => implode(",",$article->getTags()),
                'creation_time' => $article->getCrTime(),
                'last_modified' => $article->getLastMod()
            ];
            $user = new User([]);
            $filter = ['_id' => new ObjectId("{$authorId}")];
            $userGot = $user->user_get($filter);
            if($userGot){
                //User getted by author id field of article collection
                $response['article']['author'] = [
                    'username' => $user->getUsername()
                ];
            }
            $response['done'] = true;
        }//if($got){
        else{
            $response['msg'] = "Impossibile trovare l'articolo con permalink {$permalink}";
            $response['notfound'] = true;
        }
            
    }catch(Exception $e){
        $response['msg'] = C::ERROR_UNKNOWN;
    }
}//if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>