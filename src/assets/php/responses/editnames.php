<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\UpdateNamesController;
use AngularBlog\Classes\Account\UpdateNamesView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for edit names PUT route
 */
class EditNames{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH],$put["new_name"],$put["new_surname"]) && $headers[C::KEY_AUTH] != "" && $put["new_name"] != "" && $put["new_surname"] != ""){
            if(preg_match(User::$regex["name"],$put["new_name"]) && preg_match(User::$regex["surname"],$put["new_surname"])){
                $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
                $user_data = ["name" => $put["new_name"], "surname" => $put["new_surname"]];
                try{
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->load();
                    $token = new Token($token_data);
                    $user = new User($user_data);
                    $unc_data = [ "token" => $token, "user" => $user ];
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
                    $response[C::KEY_CODE] = $unv->getResponseCode();
                }catch(Exception $e){
                    $response[C::KEY_CODE] = 500;
                    $response[C::KEY_MESSAGE] = C::NAMES_UPDATE_ERROR;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = "Il formato del nome o del cognome non è corretto";
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