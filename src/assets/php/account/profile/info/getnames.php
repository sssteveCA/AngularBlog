<?php

require_once("../../../cors.php");
require_once("../../../interfaces/constants.php");
require_once("../../../interfaces/exception_messages.php");
require_once("../../../interfaces/from_errors.php");
require_once("../../../interfaces/model_errors.php");
require_once("../../../interfaces/token_errors.php");
require_once("../../../interfaces/user_errors.php");
require_once("../../../interfaces/account/getnamescontroller_errors.php");
require_once("../../../interfaces/account/getnamesview_errors.php");
require_once("../../../traits/error.trait.php");
require_once("../../../traits/message.trait.php");
require_once("../../../traits/messagearray.trait.php");
require_once("../../../traits/response.trait.php");
require_once("../../../traits/responsemultiple.trait.php");
require_once("../../../../../../vendor/autoload.php");
require_once("../../../classes/model.php");
require_once("../../../classes/token.php");
require_once("../../../classes/user.php");
require_once("../../../classes/account/getnamescontroller.php");
require_once("../../../classes/account/getnamesview.php");

use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Account\GetNamesControllerErrors as Gnce;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Account\GetNamesController;
use AngularBlog\Classes\Account\GetNamesView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;


$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => "", "data" => []
];

if(isset($_GET["token_key"])){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $_GET["token_key"]];
    try{
        $token = new Token($token_data);
        $user = new User([]);
        $gnc_data = [ 'token' => $token, 'user' => $user ];
        $gnc = new GetNamesController($gnc_data);
        $gnv = new GetNamesView($gnc);
        if($gnv->isDone()){
            $response["done"] = true;
            $data = $gnv->getMessageArray();
            $response["data"]["name"] = $data["name"];
            $response["data"]["surname"] = $data["surname"];
        }//if($gnv->isDone()){
        else{
            if($guc->getErrno() == Gnce::FROM_TOKEN){
                if($guc->getToken()->getErrno() == Te::TOKENEXPIRED)
                    $response[C::KEY_EXPIRED] = true;
            }
        }//else di if($gnv->isDone()){
        http_response_code($gnv->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $error = $e->getMessage();
        file_put_contents(C::FILE_LOG, "{$error}\r\n",FILE_APPEND);
    }
}//if(isset($_GET["token_key"])){
else
    $response[C::KEY_MESSAGE] = "Fornisci un token di autorizzazione per continuare";

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_SLASHES);
?>