<?php

use AngularBlog\Classes\Account\UpdatePasswordController;
use AngularBlog\Classes\Account\UpdatePasswordView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
];
$headers = getallheaders();

$input = file_get_contents("php://input");
$update = json_decode($input,true);

if(isset($headers[C::KEY_AUTH],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
    if(preg_match(User::$regex["password"],$update["new_password"])){
        if($update["new_password"] == $update["conf_new_password"]){
            $token_data = [ "token_key" => $headers[C::KEY_AUTH]];
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
                $dotenv->safeLoad();
                $token = new Token($token_data);
                $user = new User();
                $upc_data = [
                    'new_password' => $update['new_password'], 'old_password' => $update['old_password'], 'token' => $token, 'user' => $user
                ];
                $upController = new UpdatePasswordController($upc_data);
                $upView = new UpdatePasswordView($upController);
                $response[C::KEY_MESSAGE] = $upView->getMessage();
                if($upView->isDone()){
                    $response[C::KEY_DONE] = true;
                }// if($upView->isDone()){
                else{
                    $errnoT = $upController->getToken()->getErrno();
                    if($errnoT == Te::TOKENEXPIRED){
                        $response[C::KEY_EXPIRED] = true;
                        $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                    }
                }//else di if($upView->isDone()){
                http_response_code($upView->getResponseCode());
            }catch(Exception $e){
                http_response_code(500);
                $response[C::KEY_MESSAGE] = C::PASSWORD_UPDATE_ERROR;
            }
        }//if($update["new_password"] == $update["conf_new_password"]){
        else{
            http_response_code(400);
            $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
        } 
    }//if(preg_match(User::$regex["new_password"],$update["new_password"])){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = 'La nuova password inserita ha un formato non valido';
    }
}//if(isset($headers[C::KEY_AUTH],$update["conf_new_password"],$update["new_password"],$update["old_password"])){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>