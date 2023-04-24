<?php

require_once("../../../../../../vendor/autoload.php");

use AngularBlog\Classes\Account\UpdateNamesController;
use AngularBlog\Classes\Account\UpdateNamesView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
];

$input = file_get_contents("php://input");
$update = json_decode($input,true);
$headers = getallheaders();

if(isset($headers[C::KEY_AUTH],$update["new_name"],$update["new_surname"]) && $headers[C::KEY_AUTH] != "" && $update["new_name"] != "" && $update["new_surname"] != ""){
    if(preg_match(User::$regex["name"],$update["new_name"]) && preg_match(User::$regex["surname"],$update["new_surname"])){
        $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
        $user_data = ["name" => $update["new_name"], "surname" => $update["new_surname"]];
        try{
            $dotenv = Dotenv::createImmutable(__DIR__."/../../../../../../");
            $dotenv->safeLoad();
            $token = new Token($token_data);
            $user = new User($user_data);
            $unc_data = [
                "token" => $token, "user" => $user
            ];
            $unc = new UpdateNamesController($unc_data);
            $unv = new UpdateNamesView($unc);
            $response[C::KEY_MESSAGE] = $unv->getMessage();
            if($unv->isDone()) 
                $response[C::KEY_DONE] = true;
            else{
                $errnoT = $unc->getToken()->getErrno();
                if($errnoT == Te::TOKENEXPIRED){
                    $response[C::KEY_EXPIRED] = true;
                    $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                } 
            }
            http_response_code($unv->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            $response[C::KEY_MESSAGE] = C::NAMES_UPDATE_ERROR;
        }
    }//if(preg_match(User::$regex["name"],$update["name"]) && preg_match(User::$regex["surname"],$update["surname"])){
    else{
        http_response_code(400);
        $response[C::KEY_MESSAGE] = "Il formato del nome o del cognome non è corretto";
    }
}//if(isset($headers[C::KEY_AUTH],$update["name"],$update["surname"]) && $headers[C::KEY_AUTH] != "" && $update["name"] != "" && $update["surname"] != ""){
else{
    http_response_code(400);
    $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
}

echo json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>