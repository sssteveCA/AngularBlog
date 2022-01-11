<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("config.php");

$response = array();
$response['msg'] = '';
$response['done'] = ''; 
$response['post'] = $_POST;

echo json_encode($response);
?>