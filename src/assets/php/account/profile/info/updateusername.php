<?php

require_once("../../../cors.php");
//require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/account/userauthorizedcontroller_errors.php");
require_once("../../../interfaces/account/userauthorizedview_errors.php");
require_once("../../../interfaces/account/updateusernamecontroller_errors.php");
require_once("../../../interfaces/account/updateusernameview_errors.php");
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
require_once("../../../classes/account/updateusernamecontroller.php");
require_once("../../../classes/account/updateusernameview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Account\UpdateUsernameController;
use AngularBlog\Classes\Account\UpdateUsernameView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;

$response = [
    "done" => false, "expired" => false, "msg" => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($update["token_key"],$update["new_username"],$update["password"]) && $update["token_key"] != "" && $update["new_username"] != "" && $update['password'] != ""){
    if(preg_match(User::$regex["username"],$update["new_username"])){
        $token_data = [ "token_key" => $update["token_key"] ];
        $user_data = [ "username" => $update["new_username"], "password" => $update["password"]]; 
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User($user_data);
            $uuc_data = [
                "token" => $token, "user" => $user
            ];
            $uuController = new UpdateUsernameController($uuc_data);
            $uuView = new UpdateUsernameView($uuController);
            $response['msg'] = $uuView->getMessage();
            if($uuView->isDone()){
                $response['done'] = true;
                $response['new_username'] = $update['new_username'];
            }  
            else{
            $errnoT = $uuController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response['expired'] = true;
                    $response['msg'] = Te::TOKENEXPIRED_MSG;
                }           
            }
        }catch(Exception $e){
            echo "Exception message => ".$e->getMessage()."\r\n";
            $response['msg'] = C::USERNAME_UPDATE_ERROR;
        }
    }//if(preg_match(User::$regex["username"],$update["new_username"])){
    else{
        $response["msg"] = "Il formato del nome utente inserito non è corretto";
    }  
}//if(isset($update["token_key"],$update["new_username"],$update["password"]) && $update["token_key"] != "" && $update["new_username"] != "" && $update['password'] != ""){
else{
    $response["msg"] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>