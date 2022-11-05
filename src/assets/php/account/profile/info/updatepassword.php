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
require_once("../../../interfaces/account/updatepasswordcontroller_errors.php");
require_once("../../../interfaces/account/updatepasswordview_errors.php");
require_once("../../../traits/authorized.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/updatepasswordcontroller.php");
require_once("../../../classes/account/updatepasswordview.php");

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
    if(preg_match(User::$regex["new_password"],$update["new_password"])){
        $token_data = [ "token_key" => $update["token_key"]];
        try{
            $token = new Token($token_data);
            $user = new User();
        }catch(Exception $e){

        }
    }//if(preg_match(User::$regex["new_password"],$update["new_password"])){
    else{
        $response['msg'] = 'La nuova password inserita ha un formato non valido';
    }
}//if(isset($update["token_key"],$update["conf_new_password"],$update["new_password"],$update["old_password"])){

echo json_encode($response);
?>