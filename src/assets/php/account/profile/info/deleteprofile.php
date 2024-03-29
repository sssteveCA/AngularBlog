<?php

require_once("../../../interfaces/article/articlelist_errors.php");
require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Classes\Account\DeleteAccountController;
use AngularBlog\Classes\Account\DeleteAccountView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
];
$headers = getallheaders();

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

if(isset($headers[C::KEY_AUTH],$delete["password"],$delete["conf_password"])){
    if($delete["password"] == $delete["conf_password"]){
        $token_data = [ "token_key" => $headers[C::KEY_AUTH]];
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User();
            $dac_data = [
                "conf_password" => $delete["conf_password"],
                "password" => $delete["password"],
                "token" => $token, "user" => $user
            ];
            $dacController = new DeleteAccountController($dac_data);
            $dacView = new DeleteAccountView($dacController);
            $response[C::KEY_MESSAGE] = $dacView->getMessage();
            if($dacView->isDone()){
                $response[C::KEY_DONE] = true;
            }
            else{
                $errnoT = $dacController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response[C::KEY_EXPIRED] = true;
                    $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                }
            }
            http_response_code($dacView->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            $response[C::KEY_MESSAGE] = C::ACCOUNTDELETE_ERROR;
        }
    }//if($delete["password"] == $delete["conf_password"]){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
    }
}//if(isset($headers[C::KEY_AUTH],$delete["password"],$delete["conf_password"])){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>