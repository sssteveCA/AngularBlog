<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Account\UpdateUsernameController;
use AngularBlog\Classes\Account\UpdateUsernameView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for edit username PUT route
 */
class EditUsername{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH],$put["new_username"],$put["password"]) && $headers[C::KEY_AUTH] != "" && $put["new_username"] != "" && $put['password'] != ""){
            if(preg_match(User::$regex["username"],$put["new_username"])){
                $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
                $user_data = [ "username" => $put["new_username"], "password" => $put["password"]]; 
                try{
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->load();
                    $token = new Token($token_data);
                    $user = new User($user_data);
                    $uuc_data = [ "token" => $token, "user" => $user ];
                    $uuController = new UpdateUsernameController($uuc_data);
                    $uuView = new UpdateUsernameView($uuController);
                    $response[C::KEY_MESSAGE] = $uuView->getMessage();
                    if($uuView->isDone()){
                        $response[C::KEY_DONE] = true;
                        $response['new_username'] = $put['new_username'];
                    }
                    else{
                        $errnoT = $uuController->getToken()->getErrno();
                        if($errnoT == Te::TOKENEXPIRED){
                            $response[C::KEY_EXPIRED] = true;
                            $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                        }           
                    }
                    $response[C::KEY_CODE] = $uuView->getResponseCode();
                }catch(Exception $e){
                    $response[C::KEY_CODE] = 500;
                    $response[C::KEY_MESSAGE] = C::USERNAME_UPDATE_ERROR;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = "Il formato del nome utente inserito non è corretto";
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
        }
        return $response;
    }
}

?>