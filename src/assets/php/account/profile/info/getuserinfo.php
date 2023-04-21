<?php

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Interfaces\Account\GetUserInfoControllerErrors as Guice;
use AngularBlog\Classes\Account\GetUserInfoController;
use AngularBlog\Classes\Account\GetUserInfoView;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => "", C::KEY_DATA => []
];
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ""){
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
    $dotenv->safeLoad();
    $token_data = ["token_key" => $headers[C::KEY_AUTH]];
    try{
        $token = new Token($token_data);
        $user = new User([]);
        $guic_data = [
            'token' => $token, 'user' => $user
        ];
        $guic = new GetUserInfoController($guic_data);
        $guiv = new GetUserInfoView($guic);
        if($guiv->isDone()){
            $response[C::KEY_DONE] = true;
            $data = $guiv->getMessageArray();
            $response[C::KEY_DATA]["email"] = $data["email"];
            $response[C::KEY_DATA]["name"] = $data["name"];
            $response[C::KEY_DATA]["surname"] = $data["surname"];
            $response[C::KEY_DATA]["username"] = $data["username"];
        }
        else{
            if($guic->getErrno() == Guice::FROM_TOKEN){
                if($guic->getToken()->getErrno() == Te::TOKENEXPIRED)
                    $response[C::KEY_EXPIRED] = true;
            }
        }
        http_response_code($guiv->getResponseCode());
    }catch(Exception $e){
        http_response_code(500);
        $error = $e->getMessage();
    }
}//if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ""){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::ERROR_TOKEN_MISSED;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>