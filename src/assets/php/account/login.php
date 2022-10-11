<?php
session_start();

require_once("../cors.php");
require_once("../../../../config.php");
require_once("../config.php");
require_once("../interfaces/exception_messages.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/model_errors.php");
require_once("../interfaces/token_errors.php");
require_once("../interfaces/user_errors.php");
require_once("../interfaces/login/logincontroller_errors.php");
require_once("../interfaces/login/loginview_errors.php");
require_once("../vendor/autoload.php");
require_once("../traits/error.trait.php");
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
use MongoDB\Driver\Exception\BulkWriteException;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array();
$response['done'] = false;
$response['post'] = $post;

if(isset($post['username'],$post['password']) && $post['username'] != '' && $post['password'] != ''){
    try{
        $data = [
            'username' => $post['username'],
            'password' => $post['password']
        ];
        $user = new User($data);
        $loginController = new LoginController($user);
        $loginView = new LoginView($loginController);
        $logged = $loginView->isLogged();
        if($logged){
            //Correct credentials and account activated
            $token = $loginController->getToken();
            //file_put_contents(C::FILE_LOG,"Login token => ".var_export($token,true)."\r\n",FILE_APPEND);
            $response['username'] = $token->getUsername();
            $response['token_key'] = $token->getTokenKey();
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