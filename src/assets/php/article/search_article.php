<?php

require_once("../cors.php");
require_once("../../../../vendor/autoload.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/exception_messages.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/user_errors.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/article/article.php");
require_once("../classes/user.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;

$response = [
    C::KEY_DONE => false, C::KEY_MESSAGE => ''
];
$params = array();

if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $permalink = $_GET['permalink'];
    $filter = ['permalink' => $permalink];
    try{
        $article = new Article([]);
        $got = $article->article_get($filter);
        if($got){
            //Article with given permalink found
            $authorId = $article->getAuthor();
            $response[C::KEY_DATA] = [
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
            $filter = ['_id' => new ObjectId($authorId)];
            $userGot = $user->user_get($filter);
            if($userGot){
                //User getted by author id field of article collection
                $response[C::KEY_DATA]['author'] =  $user->getUsername();
            }
            else $response[C::KEY_DATA]['author'] = 'Sconosciuto';
            $response[C::KEY_DONE] = true;
        }//if($got){
        else{
            $response[C::KEY_MESSAGE] = "Impossibile trovare l'articolo con permalink {$permalink}";
            $response['notfound'] = true;
        }
            
    }catch(Exception $e){
        $response[C::KEY_MESSAGE] = C::ERROR_UNKNOWN;
    }
}//if(isset($_GET['permalink']) && $_GET['permalink'] != ''){
else
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>