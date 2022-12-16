<?php
require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once("../interfaces/exception_messages.php");
require_once("../interfaces/from_errors.php");
require_once("../interfaces/email/emailmanager_errors.php");
require_once('../interfaces/model_errors.php');
require_once('../interfaces/user_errors.php');
require_once('../interfaces/subscribe/registrationcontroller_errors.php');
require_once('../interfaces/subscribe/registrationview_errors.php');
require_once('../../../../vendor/autoload.php');
require_once("../traits/error.trait.php");
require_once("../traits/message.trait.php");
require_once("../traits/response.trait.php");
require_once("../traits/emailmanager.trait.php");
require_once("../classes/email/emailmanager.php");
require_once('../classes/model.php');
require_once('../classes/user.php');
require_once('../classes/subscribe/registrationcontroller.php');
require_once('../classes/subscribe/registrationview.php');

use AngularBlog\Classes\Subscribe\RegistrationController;
use AngularBlog\Classes\Subscribe\RegistrationView;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array();
$response['msg'] = '';
$response['done'] = false;

if(isset($post['name'],$post['surname'],$post['username'],$post['email'],$post['password'],$post['confPwd'],$post['subscribed'])){
    if(preg_match(User::$regex['password'],$post['password'])){
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
        $dotenv->safeLoad();
        try{
            $data = array(
                'name' => $post['name'],
                'surname' => $post['surname'],
                'username' => $post['username'],
                'email' => $post['email'],
                'password' => $post['password'],
                'subscribed' => $post['subscribed'],
            );
            $user = new User($data);
            $rc = new RegistrationController($user);
            $rv = new RegistrationView($rc);
            if($rc->getErrno() == 0)$response['done'] = true;
            $response['msg'] = $rv->getMessage();
            http_response_code($rv->getResponseCode());
        }
        catch(Exception $e){
            http_response_code(500);
            file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
            $response['msg'] = C::REG_ERROR;
        }
    }//if(preg_match(User::$regex['password'],$_POST['password'])){
    else{
        http_response_code(400);
        $response['msg'] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
    }
        
}//if(isset($_POST['name'],$_POST['surname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'],$_POST['subscribed'])){
else{
    http_response_code(400);
    $response['msg'] = C::FILL_ALL_FIELDS;
}
    

echo json_encode($response);
?>