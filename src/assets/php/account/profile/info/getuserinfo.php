<?php

require_once("../../../../../../vendor/autoload.php");

use Dotenv\Dotenv;

$response = [
    "done" => false, "expired" => false, "msg" => "","data" => []
];

if(isset($_GET["token_key"]) && $_GET["token_key"] != ""){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->load();
    $token_data = ["token_key" => $_GET["token_key"]];

}//if(isset($_GET["token_key"]) && $_GET["token_key"] != ""){
else
    $response["msg"] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>