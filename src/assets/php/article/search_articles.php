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
    $response = Article::getMultiData($field,$query);
}//if(isset($_POST['query']) && $_POST['query'] != ''){
else{
    $response['msg'] = 'Inserire i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>