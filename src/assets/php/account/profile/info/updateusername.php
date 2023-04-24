<?php

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Account\UpdateUsernameController;
use AngularBlog\Classes\Account\UpdateUsernameView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH],$update["new_username"],$update["password"]) && $headers[C::KEY_AUTH] != "" && $update["new_username"] != "" && $update['password'] != ""){
    if(preg_match(User::$regex["username"],$update["new_username"])){
        $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
        $user_data = [ "username" => $update["new_username"], "password" => $update["password"]]; 
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User($user_data);
            $uuc_data = [
                "token" => $token, "user" => $user
            ];
            $uuController = new UpdateUsernameController($uuc_data);
            $uuView = new UpdateUsernameView($uuController);
            $response[C::KEY_MESSAGE] = $uuView->getMessage();
            if($uuView->isDone()){
                $response[C::KEY_DONE] = true;
                $response['new_username'] = $update['new_username'];
            }  
            else{
            $errnoT = $uuController->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response[C::KEY_EXPIRED] = true;
                    $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                }           
            }
            http_response_code($uuView->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            $response[C::KEY_MESSAGE] = C::USERNAME_UPDATE_ERROR;
        }
    }//if(preg_match(User::$regex["username"],$update["new_username"])){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = "Il formato del nome utente inserito non è corretto";
    }  
}//if(isset($headers[C::KEY_AUTH],$update["new_username"],$update["password"]) && $headers[C::KEY_AUTH] != "" && $update["new_username"] != "" && $update['password'] != ""){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>