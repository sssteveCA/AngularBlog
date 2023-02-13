<?php

require_once("../cors.php");
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
    C::KEY_DONE => false, C::KEY_EMPTY => false, C::KEY_MESSAGE => '',
];
$field = 'title';

$input = file_get_contents("php://input");
$post = json_decode($input,true);

if(isset($post['query']) && $post['query'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $query = $post['query'];
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
                $response[C::KEY_DATA][] = array(
                    'title' => $article->getTitle(),
                    'author' => $article->getAuthor(),
                    'permalink' => $article->getPermalink(),
                    'introtext' => $article->getIntrotext(),
                );
            }//foreach($articles as $article){
            $response[C::KEY_DONE] = true;
        }//if($found){
        else{
            $response[C::KEY_EMPTY] = true;
            $response[C::KEY_MESSAGE] = 'La ricerca di '.$query.' non ha fornito alcun risultato';
        }
            
    }catch(Exception $e){
        //file_put_contents(C::FILE_LOG,"Search articles Exception => ".var_export($e,true)."\r\n",FILE_APPEND);
        $response[C::KEY_MESSAGE] = C::SEARCH_ERROR;
    }
}//if(isset($post['query']) && $post['query'] != ''){
else 
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>