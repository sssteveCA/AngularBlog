<?php

require_once("../../../interfaces/article/articlelist_errors.php");
require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Myarticles\GetController;
use AngularBlog\Classes\Myarticles\GetView;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_MESSAGE => ''
];
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $data = [
        'token_key' => $headers[C::KEY_AUTH]
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
            $response[C::KEY_DONE] = true;
        }
        else
            $response[C::KEY_MESSAGE] = $getView->getMessage();
        http_response_code($getView->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $response[C::KEY_MESSAGE] = C::SEARCH_ERROR;
        file_put_contents(C::FILE_LOG,var_export($e->getMessage(),true)."\r\n",FILE_APPEND);
    }  
}//if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>