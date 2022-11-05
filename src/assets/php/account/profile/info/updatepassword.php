<?php

use AngularBlog\Classes\Account\UpdatePasswordController;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;

require_once("../../../cors.php");
require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
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

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
    if(preg_match(User::$regex["new_password"],$update["new_password"])){
        if($update["new_password"] == $update["conf_new_password"]){
            $token_data = [ "token_key" => $update["token_key"]];
            try{
                $token = new Token($token_data);
                $user = new User();
                $upc_data = [
                    'new_password' => $update['new_password'], 'old_password' => $update['old_password'], 'token' => $token, 'user' => $user
                ];
                $upc = new UpdatePasswordController($upc_data);
            }catch(Exception $e){
                http_response_code(500);
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

echo json_encode($response);
?>