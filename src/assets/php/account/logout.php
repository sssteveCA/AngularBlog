<?php

require_once("../cors.php");
require_once("../../../../config.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/exception_messages.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/token_errors.php");
require_once("../interfaces/logout/logoutcontrollererrors.php");
require_once("../interfaces/logout/logoutviewerrors.php");
require_once('../../../../vendor/autoload.php');
require_once("../traits/error.trait.php");
require_once("../traits/message.trait.php");
require_once("../traits/response.trait.php");
require_once("../classes/model.php");
require_once("../classes/token.php");
require_once("../classes/logout/logoutcontroller.php");
require_once("../classes/logout/logoutview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Logout\LogoutView;
use AngularBlog\Classes\Logout\LogoutController;
use Dotenv\Dotenv;

$response = array(
    'msg' => '',
    'done' => false
);

if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
    $dotenv->safeLoad();
    $token_key = $_GET['token_key'];
    $filter = ['token_key' => $token_key];
    try{
        $token = new Token();
        $get = $token->token_get($filter);
        if($get){
            //Found in DB a collection with token key passed from client
            $logoutController = new LogoutController($token);
            $logoutView = new LogoutView($logoutController);
            $logout = $logoutView->isLogout();
            if($logout){
                //Server side logout done
                $response['done'] = true;
            }
            else
                $response['msg'] = $logoutView->getMessage();
            http_response_code($logoutView->getResponseCode());
        }//if($get){
        else{
            http_response_code(404);
            $response['msg'] = C::LOGOUT_ERROR_USERNOTFOUND;
        }          
    }catch(Exception $e){
        http_response_code(500);
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
        $response['msg'] = C::LOGOUT_ERROR;
    }
}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
else{
    http_response_code(400);
    $response['msg'] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>