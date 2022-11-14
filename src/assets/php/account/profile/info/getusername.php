<?php

use AngularBlog\Classes\Account\GetUsernameController;
use AngularBlog\Interfaces\Account\GetUsernameControllerErrors as Guce;
use AngularBlog\Classes\Info\GetUsernameView;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

require_once("../../../cors.php");
//require_once("../../../../../../config.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/account/getusernamecontroller_errors.php");
require_once("../../../interfaces/account/getusernameview_errors.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../traits/error.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/getusernamecontroller.php");
require_once("../../../classes/account/getusernameview.php");

$response = [
    "done" => false, "expired" => false, "msg" => "", "username" => ""
];

if(isset($_GET["token_key"])){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $_GET["token_key"]];
    try{
        $token = new Token($token_data);
        $user = new User([]);
        $guc_data = [
            'token' => $token, 'user' => $user
        ];
        $guc = new GetUsernameController($guc_data);
        $guv = new GetUsernameView($guc);
        if($guv->isDone()){
            $response["done"] = true;
            $response["username"] = $guv->getMessage();
        }//if($guv->isDone()){
        else{
            if($guc->getErrno() == Guce::FROM_TOKEN){
                if($guc->getToken()->getErrno() == Te::TOKENEXPIRED)
                    $response["expired"] = true;
            }
        }//else di if($guv->isDone()){
        http_response_code($guv->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $error = $e->getMessage();
        file_put_contents(C::FILE_LOG, "{$error}\r\n",FILE_APPEND);
    }
}//if(isset($get["token_key"])){
else
    $response["msg"] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response);
?>