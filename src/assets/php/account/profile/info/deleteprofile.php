<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$response = [
    "done" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){
    $token_data = [ "token_key" => $delete["token_key"]];
    try{
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
        $dotenv->safeLoad();
        $token = new Token($token_data);
        $user = new User();
    }catch(Exception $e){
        http_response_code(500);
        $response['msg'] = C::ACCOUNTDELETE_ERROR;
    }
}//if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){
else{
    http_response_code(400);
    $response['msg'] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>