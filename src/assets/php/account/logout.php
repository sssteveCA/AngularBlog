<?php

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/logout/logoutcontrollererrors.php");
require_once("../interfaces/logout/logoutviewerrors.php");
require_once("../vendor/autoload.php");
require_once("../classes/model.php");
require_once("../classes/token.php");
require_once("../classes/logout/logoutcontroller.php");
require_once("../classes/logout/logoutview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Logout\LogoutView;
use AngularBlog\Classes\Logout\LogoutController;

$response = array(
    'msg' => '',
    'done' => false
);

if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
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
        }//if($get){
        else
            $response['msg'] = C::LOGOUT_ERROR_USERNOTFOUND;
    }catch(Exception $e){
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
        $response['msg'] = C::LOGOUT_ERROR;
    }
    

}//if(isset($_GET['token_key']) && $_GET['token_key'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

?>