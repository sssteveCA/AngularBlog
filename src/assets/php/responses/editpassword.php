<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\UpdatePasswordController;
use AngularBlog\Classes\Account\UpdatePasswordView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for edit account password PUT route
 */
class EditPassword{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $put = $params['put'];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH],$put["conf_new_password"],$put["new_password"],$put["old_password"])){
            if(preg_match(User::$regex["password"],$put["new_password"])){
                if($put["new_password"] == $put["conf_new_password"]){
                    $token_data = [ "token_key" => $headers[C::KEY_AUTH] ];
                    try{
                        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                        $dotenv->load();
                        $token = new Token($token_data);
                        $user = new User();
                        $upc_data = [
                            'new_password' => $put['new_password'], 'old_password' => $put['old_password'], 'token' => $token, 'user' => $user
                        ];
                        $upController = new UpdatePasswordController($upc_data);
                        $upView = new UpdatePasswordView($upController);
                        $response[C::KEY_MESSAGE] = $upView->getMessage();
                        if($upView->isDone()) $response[C::KEY_DONE] = true;
                        else{
                            $errnoT = $upController->getToken()->getErrno();
                            if($errnoT == Te::TOKENEXPIRED){
                                $response[C::KEY_EXPIRED] = true;
                                $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                            }
                        }
                        $response[C::KEY_CODE] = $upView->getResponseCode();
                    }catch(Exception $e){
                        $response[C::KEY_CODE] = 500;
                        $response[C::KEY_MESSAGE] = C::PASSWORD_UPDATE_ERROR;
                    }
                }
                else{
                    $response[C::KEY_CODE] = 400;
                    $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = 'La nuova password inserita ha un formato non valido';
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