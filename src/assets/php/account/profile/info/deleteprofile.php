<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/account/userauthorizedcontroller_errors.php");
require_once("../../../interfaces/account/deleteaccountcontroller_errors.php");
require_once("../../../interfaces/account/deleteaccountview_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../traits/authorized.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/userauthorizedcontroller.php");
require_once("../../../classes/account/deleteaccountcontroller.php");
require_once("../../../classes/account/deleteaccountview.php");

use AngularBlog\Classes\Account\DeleteAccountController;
use AngularBlog\Classes\Account\DeleteAccountView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$delete = json_decode($input,true);

if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){
    if($delete["password"] == $delete["conf_password"]){
        $token_data = [ "token_key" => $delete["token_key"]];
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
            $response['msg'] = $dacView->getMessage();
            if($dacView->isDone()){
                $response['done'] = true;
            }
            else{
                $errnoT = $dacController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response['expired'] = true;
                    $response['msg'] = Te::TOKENEXPIRED_MSG;
                }
            }
            http_response_code($dacView->getResponseCode());
        }catch(Exception $e){
            echo "deleteprofile.php exception => ".var_export($e->getMessage(),true)."\r\n";
            http_response_code(500);
            $response['msg'] = C::ACCOUNTDELETE_ERROR;
        }
    }//if($delete["password"] == $delete["conf_password"]){
    else{
        http_response_code(400);
        $response['msg'] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
    }
}//if(isset($delete["token_key"],$delete["password"],$delete["conf_password"])){
else{
    http_response_code(400);
    $response['msg'] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>