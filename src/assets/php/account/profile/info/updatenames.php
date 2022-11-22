<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/account/userauthorizedcontroller_errors.php");
require_once("../../../interfaces/account/userauthorizedview_errors.php");
require_once("../../../exceptions/notokeninstanceexception.php");
require_once("../../../exceptions/nouserinstanceexception.php");
require_once("../../../exceptions/tokentypemismatchexception.php");
require_once("../../../exceptions/usertypemismatchexception.php");
require_once("../../../traits/authorized.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/userauthorizedcontroller.php");
require_once("../../../classes/account/userauthorizedview.php");

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["name"],$update["surname"]) && $update["token_key"] != "" && $update["name"] != "" && $update["surname"] != ""){
    if(preg_match(User::$regex["name"],$update["name"]) && preg_match(User::$regex["surname"],$update["surname"])){
        $token_data = [ "token_key" => $update["token_key"] ];
        $user_data = ["name" => $update["name"], "surname" => $update["surname"]];
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User($user_data);
            $unc_data = [
                "token" => $token, "user" => $user
            ];
        }catch(Exception $e){
            http_response_code(500);
            //echo "Exception message => ".$e->getMessage()."\r\n";
            $response["msg"] = C::NAMES_UPDATE_ERROR;
        }
    }//if(preg_match(User::$regex["name"],$update["name"]) && preg_match(User::$regex["surname"],$update["surname"])){
    else{
        http_response_code(400);
        $response["msg"] = "Il formato del nome o del cognome non è corretto";
    }
}//if(isset($update["token_key"],$update["name"],$update["surname"]) && $update["token_key"] != "" && $update["name"] != "" && $update["surname"] != ""){
else{
    http_response_code(400);
    $response["msg"] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>