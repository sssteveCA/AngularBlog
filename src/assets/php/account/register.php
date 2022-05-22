<?php

require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once('../interfaces/bloguser_errors.php');
require_once('../class/bloguser.php');

use AngularBlog\Classes\BlogUser;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\BlogUserErrors as Bue;

$response = array();
$response['msg'] = '';
$response['done'] = false;

$response['post'] = $_POST;

if(isset($_POST['name'],$_POST['surname'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'])){
    if($_POST['password'] == $_POST['confPwd']){
        if(preg_match(BlogUser::$regex['password'],$_POST['password'])){
            try{
                $data = array(
                    'name' => $_POST['name'],
                    'surname' => $_POST['surname'],
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                );
                $bUser = new BlogUser($data);
                $reg = $bUser->registration();
                if($reg){
                    //Data added to DB and Mail successufly sent
                    $response['done'] = true;
                    $response['msg'] = C::EMAIL_ACCOUNT_CREATED;
                }//if($reg){
                else{
                    $errno = $bUser->getErrno();
                    switch($errno){
                        case Bue::INVALIDDATAFORMAT:
                        case Bue::USERNAMEMAILEXIST:
                        case Bue::MAILNOTSENT:
                            $response['msg'] = $bUser->getError();
                            break;
                        default:
                            $response['msg'] = C::REG_ERROR;
                            break;
                    }
                }//else di if($reg){
            }
            catch(Exception $e){
                $response['msg'] = C::ERROR_UNKNOWN;
            }
        }//if(preg_match(BlogUser::$regex['password'],$_POST['password'])){
    }//if($_POST['password'] == $_POST['confPwd']){
    else
        $response['msg'] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
}//if(isset($_POST['nome'],$_POST['cognome'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'])){
else
    $response['msg'] = C::FILL_ALL_FIELDS;

echo json_encode($response);
?>