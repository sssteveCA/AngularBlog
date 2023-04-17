<?php

require_once("../../../../../../vendor/autoload.php");

use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;

$response = [
    C::KEY_DONE => false, C::KEY_MESSAGE => ""
];
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH],$_REQUEST['action_id']) && $headers[C::KEY_AUTH] != "" && $_REQUEST['action_id'] != ""){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    try{

    }catch(Exception $e){
        http_response_code(500);
        $response[C::KEY_MESSAGE] = C::HISTORYITEM_DELETE_ERROR;

    }
}//if(isset($headers[C::KEY_AUTH],$_REQUEST['action_id']) && $headers[C::KEY_AUTH] != "" && $_REQUEST['action_id'] != ""){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>