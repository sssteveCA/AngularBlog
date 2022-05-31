<?php
require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once('../interfaces/model_errors.php');
require_once('../interfaces/user_errors.php');
require_once('../interfaces/subscribe/registrationcontroller_errors.php');
require_once('../interfaces/subscribe/registrationview_errors.php');
require_once('../vendor/autoload.php');
require_once('../classes/model.php');
require_once('../classes/user.php');
require_once('../classes/subscribe/registrationcontroller.php');
require_once('../classes/subscribe/registrationview.php');

use AngularBlog\Classes\Subscribe\RegistrationController;
use AngularBlog\Classes\Subscribe\RegistrationView;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;

$response = array();
$response['msg'] = '';
$response['done'] = false;

if(isset($_POST['name'],$_POST['surname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'],$_POST['subscribed'])){
    if(preg_match(User::$regex['password'],$_POST['password'])){
        try{
            $data = array(
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'subscribed' => $_POST['subscribed'],
            );
            $user = new User($data);
            $rc = new RegistrationController($user);
            $rv = new RegistrationView($rc);
            if($rc->getErrno() == 0)$response['done'] = true;
            $response['msg'] = $rv->getMessage();
        }
        catch(Exception $e){
            file_put_contents(C::FILE_LOG,$e->getMessage()."\r\n",FILE_APPEND);
            $response['msg'] = C::REG_ERROR;
        }
    }//if(preg_match(User::$regex['password'],$_POST['password'])){
    else 
        $response['msg'] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
}//if(isset($_POST['name'],$_POST['surname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'],$_POST['subscribed'])){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);
?>