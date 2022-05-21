<?php

require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once('../interfaces/bloguser_errors.php');
require_once('../class/bloguser.php');

use AngularBlog\Classes\BlogUser;
use AngularBlog\Interfaces\Constants as C;

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
                    $send = $bUser->sendEmail();
                    if($send){
                        //Mail successufly sent
                        $response['done'] = true;
                        $response['msg'] = C::EMAIL_ACCOUNT_CREATED;
                    }
                    else{
                        $errno = $bUser->getErrno();
                        $response['msg'] = 'Errore durante l\' invio della mail. Codice '.$errno;
                    } 
                }//if($reg){
                else{
                    $errno = $bUser->getErrno();
                    $response['msg'] = 'Errore durante la registrazione dell\' account. Codice '.$errno;
                }
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