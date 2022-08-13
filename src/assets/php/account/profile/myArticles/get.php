<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/models_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/article/article_errors.php");
require_once("../../../interfaces/article/articlelist_errors.php");
require_once("../../../interfaces/myarticles/getcontrollererrors.php");
require_once("../../../interfaces/myarticles/getviewerrors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../classes/model.php");
require_once("../../../classes/models.php");
require_once("../../../classes/token.php");
require_once("../../../classes/article/article.php");
require_once("../../../classes/article/articlelist.php");
require_once("../../../classes/myarticles/getcontroller.php");
require_once("../../../classes/myarticles/getview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Myarticles\GetController;
use AngularBlog\Classes\Myarticles\GetView;

$response = array();
$response['done'] = false;
$response['msg'] = '';

if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
    $data = [
        'token_key' => $_GET['token_key']
    ];
    try{
        $getController = new GetController($data);
        $getView = new GetView($getController);
        if($getView->articlesFound()){
            //At least one article found
            $articles = $getController->getArticleList()->getResults();
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
        }
        else
            $response['msg'] = $getView->getMessage();
    }catch(Exception $e){
        $response['msg'] = C::SEARCH_ERROR;
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
    }
    
}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>