<?php

use AngularBlog\Classes\Account\UpdatePasswordController;
use AngularBlog\Classes\Account\UpdatePasswordView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;

require_once("../../../cors.php");
//require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/account/userauthorizedcontroller_errors.php");
require_once("../../../interfaces/account/updatepasswordcontroller_errors.php");
require_once("../../../interfaces/account/updatepasswordview_errors.php");
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
require_once("../../../classes/account/updatepasswordcontroller.php");
require_once("../../../classes/account/updatepasswordview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
    if(preg_match(User::$regex["password"],$update["new_password"])){
        if($update["new_password"] == $update["conf_new_password"]){
            $token_data = [ "token_key" => $update["token_key"]];
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
                $dotenv->safeLoad();
                $token = new Token($token_data);
                $user = new User();
                $upc_data = [
                    'new_password' => $update['new_password'], 'old_password' => $update['old_password'], 'token' => $token, 'user' => $user
                ];
                $upController = new UpdatePasswordController($upc_data);
                $upView = new UpdatePasswordView($upController);
                $response['msg'] = $upView->getMessage();
                if($upView->isDone()){
                    $response['done'] = true;
                }// if($upView->isDone()){
                else{
                    $errnoT = $upController->getToken()->getErrno();
                    if($errnoT == Te::TOKENEXPIRED){
                        $response['expired'] = true;
                        $response['msg'] = Te::TOKENEXPIRED_MSG;
                    }
                }//else di if($upView->isDone()){
                http_response_code($upView->getResponseCode());
            }catch(Exception $e){
                http_response_code(500);
                //echo "updatepassword.php exception =>".var_export($e->getMessage(),true)."\r\n";
                $response['msg'] = C::PASSWORD_UPDATE_ERROR;
            }
        }//if($update["new_password"] == $update["conf_new_password"]){
        else{
            http_response_code(400);
            $response['msg'] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
        } 
    }//if(preg_match(User::$regex["new_password"],$update["new_password"])){
    else{
        http_response_code(400);
        $response['msg'] = 'La nuova password inserita ha un formato non valido';
    }
}//if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
else{
    http_response_code(400);
    $response['msg'] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>