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
require_once("../../../interfaces/account/updatenamescontroller_errors.php");
require_once("../../../interfaces/account/updatenamesview_errors.php");
require_once("../../../interfaces/action/action_errors.php");
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
require_once("../../../classes/action/action.php");
require_once("../../../classes/account/userauthorizedcontroller.php");
require_once("../../../classes/account/userauthorizedview.php");
require_once("../../../classes/account/updatenamescontroller.php");
require_once("../../../classes/account/updatenamesview.php");

use AngularBlog\Classes\Account\UpdateNamesController;
use AngularBlog\Classes\Account\UpdateNamesView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH],$update["new_name"],$update["new_surname"]) && $headers[C::KEY_AUTH] != "" && $update["new_name"] != "" && $update["new_surname"] != ""){
    if(preg_match(User::$regex["name"],$update["new_name"]) && preg_match(User::$regex["surname"],$update["new_surname"])){
        $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
        $user_data = ["name" => $update["new_name"], "surname" => $update["new_surname"]];
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User($user_data);
            $unc_data = [
                "token" => $token, "user" => $user
            ];
            $unc = new UpdateNamesController($unc_data);
            $unv = new UpdateNamesView($unc);
            $response[C::KEY_MESSAGE] = $unv->getMessage();
            if($unv->isDone()) 
                $response[C::KEY_DONE] = true;
            else{
                $errnoT = $unc->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response[C::KEY_EXPIRED] = true;
                    $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                } 
            }
            http_response_code($unv->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            echo "Exception message => ".$e->getMessage()."\r\n";
            $response[C::KEY_MESSAGE] = C::NAMES_UPDATE_ERROR;
        }
    }//if(preg_match(User::$regex["name"],$update["name"]) && preg_match(User::$regex["surname"],$update["surname"])){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = "Il formato del nome o del cognome non è corretto";
    }
}//if(isset($headers[C::KEY_AUTH],$update["name"],$update["surname"]) && $headers[C::KEY_AUTH] != "" && $update["name"] != "" && $update["surname"] != ""){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>