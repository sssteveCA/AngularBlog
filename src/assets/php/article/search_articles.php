<?php

require_once("../cors.php");
//require_once("../../../../config.php");
require_once("../../../../vendor/autoload.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/article/articlelist_errors.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/models.php");
require_once("../classes/article/article.php");
require_once("../classes/article/articlelist.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\ArticleList;
use Dotenv\Dotenv;
use MongoDB\BSON\Regex;

$response = [
    'msg' => '',
    'done' => false
];
$field = 'title';

if(isset($_POST['query']) && $_POST['query'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $query = $_POST['query'];
    try{
        $al = new ArticleList();
        $regex = new Regex($query,'i');
        $filter = array(
            'title' =>  $regex
        );
        //file_put_contents(C::FILE_LOG,"Search articles filter => ".var_export($filter,true)."\r\n",FILE_APPEND);
        $found = $al->articlelist_get($filter);
        if($found){
            //At least one article found
            $articles = $al->getResults();
            foreach($articles as $article){
                $response['articles'][] = array(
                    'id' => $article->getId(),
                    'title' => $article->getTitle(),
                    'author' => $article->getAuthor(),
                    'permalink' => $article->getPermalink(),
                    'content' => $article->getContent(),
                    'introtext' => $article->getIntrotext(),
                    'categories' => implode(",",$article->getCategories()),
                    'tags' => implode(",",$article->getTags()),
                    'creation_time' => $article->getCrTime(),
                    'last_modified' => $article->getLastMod()
                );
            }//foreach($articles as $article){
            $response['done'] = true;
        }//if($found){
        else
            $response['msg'] = 'La ricerca di '.$query.' non ha fornito alcun risultato';
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,"Search articles Exception => ".var_export($e,true)."\r\n",FILE_APPEND);
        $response['msg'] = C::SEARCH_ERROR;
    }
}//if(isset($_POST['query']) && $_POST['query'] != ''){
else 
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);
?>