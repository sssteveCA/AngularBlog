<?php
session_start();

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/user_errors.php");
require_once("../interfaces/login/logincontroller_errors.php");
require_once("../interfaces/login/loginview_errors.php");
require_once("../vendor/autoload.php");
require_once("../classes/model.php");
require_once("../classes/user.php");
require_once("../classes/token.php");
require_once("../classes/login/logincontroller.php");
require_once("../classes/login/loginview.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Login\LoginController;
use AngularBlog\Classes\Login\LoginView;
use AngularBlog\Classes\Token;

$response = array();
$response['done'] = false;
$response['post'] = $_POST;

if(isset($_POST['username'],$_POST['password']) && $_POST['username'] != '' && $_POST['password'] != ''){
    try{
        $data = [
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];
        $user = new User($data);
        $loginController = new LoginController($user);
        $loginView = new LoginView($loginController);
        $logged = $loginView->isLogged();
        if($logged){
            //Correct credentials and account activated
            $token = $loginController->getToken();
            $response['username'] = $token->getUsername();
            $response['id'] = $token->getId();
            $response['tokn_key'] = $token->getTokenKey();
            $response['done'] = true;
        }//if($logged){
        else
            $response['msg'] = $loginView->getMessage();
        $response['error'] = $loginController->getError();
    }
    catch(Exception $e){
        file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
        $response['msg'] = C::LOGIN_ERROR;
    }
    
}//if(isset($_POST['username'],$_POST['password']) && $_POST['username'] != '' && $_POST['password'] != ''){
else
    $response['msg'] = C::FILL_ALL_FIELDS;


echo json_encode($response);
?>