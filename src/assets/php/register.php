<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

$risposta = array();
$risposta['post'] = $_POST;
$risposta['done'] = false;

echo json_encode($risposta,JSON_UNESCAPED_UNICODE);
?>