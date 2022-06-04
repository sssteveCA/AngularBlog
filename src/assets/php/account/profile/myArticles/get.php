<?php

require_once("../../../cors.php");

session_start();

$response = array();
$response['done'] = false;
$response['msg'] = '';
$response['session'] = $_SESSION;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>