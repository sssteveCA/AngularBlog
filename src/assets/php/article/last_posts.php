<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/models_errors.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/user_errors.php");
require_once("../interfaces/article/article_errors.php");
require_once("../interfaces/article/articlelist_errors.php");
require_once("../../../../vendor/autoload.php");
require_once("../traits/error.trait.php");
require_once("../classes/model.php");
require_once("../classes/models.php");
require_once("../classes/user.php");
require_once("../classes/article/article.php");
require_once("../classes/article/articlelist.php");

use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;
use MongoDB\BSON\ObjectId;

$response = [
    C::KEY_DATA => [], C::KEY_DONE => false, C::KEY_EMPTY => [], C::KEY_MESSAGE => ""
];
try{
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $al = new ArticleList();
    $options = [
        'limit' => 10,
        'sort' => ['last_modified' => -1]
    ];
    $found = $al->articlelist_get([],$options);
    if($found){
        $articles = $al->getResults();
        $i = 0;
        foreach($articles as $article){
            $response[C::KEY_DATA][$i] = array(
                'categories' => implode(",", $article->getCategories()),
                'introtext' => $article->getIntrotext(),
                'last_modified' => $article->getLastMod(),
                'tags' => implode(",", $article->getTags()),
                'title' => $article->getTitle(),
            );
            $user = new User([]);
            $got = $user->user_get(['_id' => new ObjectId($article->getAuthor())]);
            if($got) $author = $user->getUsername();
            else $author = "Autore sconosciuto";
            $response[C::KEY_DATA][$i]['author'] = $author;
            $i++;
        }//foreach($articles as $article){
        $response[C::KEY_DONE] = true;
    }//if($found){
    else{
        $response[C::KEY_EMPTY] = true;
        $response[C::KEY_MESSAGE] = C::NEWS_EMPTY;
    }
}catch(Exception $e){
    echo "last_posts.php exception => ".$e->getMessage()."\r\n";
    $response[C::KEY_MESSAGE] = C::NEWS_ERROR;
    http_response_code(500);
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);


?>