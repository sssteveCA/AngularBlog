<?php

require_once("../../cors.php");

$input = file_get_contents("php://input");
$patch = json_decode($input, true);

$response = [
    'done' => false,
    'msg' => '',
    'patch' => $patch
];

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>