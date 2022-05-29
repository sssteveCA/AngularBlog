<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("../config.php");
require_once("../class/article.php");

$response = array();
$response['msg'] = 'Ciao';
$response['done'] = false;
$field = 'title';

if(isset($_POST['query']) && $_POST['query'] != ''){
    $query = $_POST['query'];
    $results = Article::getMultiData($field,$query);
    if(isset($results['articles']) && count($results['articles']) > 0){
        //if articles were found 
        $i = 0;
        foreach($results['articles'] as $index => $article){
            $response['articles'][$i]['id'] = $article->getId();
            $response['articles'][$i]['title'] = $article->getTitle();
            $response['articles'][$i]['author'] = $article->getAuthor();
            $response['articles'][$i]['permalink'] = $article->getPermalink();
            $response['articles'][$i]['content'] = $article->getContent();
            $response['articles'][$i]['introtext'] = $article->getIntrotext();
            $response['articles'][$i]['categories'] = $article->getCategories();
            $response['articles'][$i]['tags'] = $article->getTags();
            $response['articles'][$i]['creation_time'] = $article->getCrTime();
            $response['articles'][$i]['last_modified'] = $article->getLastMod();
            $i++;
        }//foreach($results['articles'] as $index => $article){
        $response['done'] = true;
    }//if(isset($results['articles']) && count($results['articles']) > 0){
    else if(isset($results['msg']))
        $response['msg'] = $results['msg'];
}//if(isset($_POST['query']) && $_POST['query'] != ''){
else{
    $response['msg'] = 'Inserire i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>