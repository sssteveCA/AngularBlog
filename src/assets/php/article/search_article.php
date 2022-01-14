<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("../config.php");
require_once("../class/article.php");

$response = array();
$response['msg'] = '';
$response['done'] = false; 
$params = array();
//$response['post'] = $_POST;

if(isset($_POST['query']) && $_POST['query'] != ''){
    try{
        $params['permalink'] = $_POST['query'];
        $article = new Article($params);
        $get = $article->getData('permalink');
        if($get){
            //data article retrieved
            $response['article']['id'] = $article->getId();
            $response['article']['title'] = $article->getTitle();
            $response['article']['author'] = $article->getAuthor();
            $response['article']['permalink'] = $article->getPermalink();
            $response['article']['content'] = $article->getContent();
            $response['article']['introtext'] = $article->getIntrotext();
            $response['article']['categories'] = $article->getCategories();
            $response['article']['tags'] = $article->getTags();
            $response['article']['creation_time'] = $article->getCrTime();
            $response['article']['last_modified'] = $article->getLastMod();
            $response['done'] = true;
        }
        else{
            /*$errno = $article->getErrno();
            $response['msg'] = 'Errore durante la lettura dell\' articolo. Codice '.$errno;*/
            $response['notfound'] = true;
        }
        $response['queries'] = $article->getQueries();
    }
    catch(Exception $e){
        $response['msg'] = $e->getMessage();
    }
}
else{
    $response['msg'] = 'Inserire i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>