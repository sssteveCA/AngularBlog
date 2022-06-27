<?php

$input = file_get_contents('php://input');
$post = json_decode($input,true);

$response = [
    'done' => false,
    'msg' => ''
];

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>