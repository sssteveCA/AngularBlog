<?php

require_once("../../../cors.php");

$response = array(
    'done' => false,
    'msg' => ''
);

$input = file_get_contents('php://input');
$post = json_decode($input,true);
$response['post'] = $post;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>