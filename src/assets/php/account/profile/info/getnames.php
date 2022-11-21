<?php

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");

$response = [
    "done" => false, "expired" => false, "msg" => "", "name" => "", "surname" => ""
];

if(isset($_GET["token_key"])){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $_GET["token_key"]];
    try{
        $token = new Token($token_data);
        $user = new User([]);
    }catch(Exception $e){
        http_response_code(500);
    }
}
else
    $response["msg"] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES);
?>