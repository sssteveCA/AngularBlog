<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");

use AngularBlog\Interfaces\Constants as C;

$response = array(
    'done' => false,
    'msg' => ''
);

$input = file_get_contents('php://input');
$post = json_decode($input,true);
//$response['post'] = $post;

if(isset($post['token_key']) && $post['token_key'] != ''){
    $data = [
        'token_key' => $post['token_key']
    ];
}//if(isset($post['token_key']) && $post['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>