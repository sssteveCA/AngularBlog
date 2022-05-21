<?php

require_once('../cors.php');
require_once('../interfaces/constants.php');
require_once('../interfaces/bloguser_errors.php');
require_once('../class/bloguser.php');

use AngularBlog\Classes\BlogUser;

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
                    
                }//if($reg){
            }
            catch(Exception $e){

            }
        }//if(preg_match(BlogUser::$regex['password'],$_POST['password'])){
    }//if($_POST['password'] == $_POST['confPwd']){
}//if(isset($_POST['nome'],$_POST['cognome'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'])){

echo json_encode($response);
?>