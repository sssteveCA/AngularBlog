<?php

use Dotenv\Dotenv;

require_once("../../../cors.php");

$response = [
    "done" => false, "expired" => false, "msg" => "", "name" => "", "surname" => ""
];

if(isset($_GET["token_key"])){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $_GET["token_key"]];
    try{

    }catch(Exception $e){
        http_response_code(500);
    }
}
else
    $response["msg"] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES);
?>