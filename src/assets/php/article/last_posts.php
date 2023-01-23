<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/article/articlelist_errors.php");
require_once("../../../../vendor/autoload.php");
require_once("../traits/error.trait.php");
require_once("../classes/article/article.php");
require_once("../classes/article/articlelist.php");

use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Interfaces\Constants as C;

$response = [
    C::KEY_DATA => [], C::KEY_DONE => false, C::KEY_EMPTY => [], C::KEY_MESSAGE => ""
];
try{
    $al = new ArticleList();
    $filter = [
        [
            '$limit' => 10,
            '$orderby' => [ 'last_modified' => 1]
        ],  
    ];
    $found = $al->articlelist_get($filter);
    if($found){
        $articles = $al->getResults();
        foreach($articles as $article){
            $response[C::KEY_DATA][] = array(
                'author' => $article->getAuthor(),
                'categories' => implode(",", $article->getCategories()),
                'introtext' => $article->getIntrotext(),
                'last_modified' => $article->getLastMod(),
                'tags' => implode(",", $article->getTags()),
                'title' => $article->getTitle(),
            );
        }//foreach($articles as $article){
        $response[C::KEY_DONE] = true;
    }//if($found){
    else{
        $response[C::KEY_EMPTY] = true;
        $response[C::KEY_MESSAGE] = C::NEWS_EMPTY;
    }
}catch(Exception $e){
    $response[C::KEY_MESSAGE] = C::NEWS_ERROR;
    http_response_code(500);
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);


?>