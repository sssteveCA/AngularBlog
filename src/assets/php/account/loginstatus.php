<?php

require_once("../cors.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../vendor/autoload.php");
require_once("../classes/model.php");
require_once("../classes/token.php");

//This script check if user is still logged
$response = array(
    'msg' => '',
    'logged' => false
);

$post = file_get_contents('php://input');
$postDecode = json_decode($post,true);

$response['post'] = $postDecode;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>