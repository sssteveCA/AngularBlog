<?php

namespace AngularBlog\Responses;

use AngularBlog\Classes\Account\DeleteAccountController;
use AngularBlog\Classes\Account\DeleteAccountView;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for delete profile POST route
 */
class DeleteProfile{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => ""
        ];
        $post = $params['post'];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH],$post["password"],$post["conf_password"])){
            if($post["password"] == $post["conf_password"]){
                $token_data = [ "token_key" => $headers[C::KEY_AUTH]];
                try{
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->load();
                    $token = new Token($token_data);
                    $user = new User();
                    $dac_data = [
                        "conf_password" => $post["conf_password"],
                        "password" => $post["password"],
                        "token" => $token, "user" => $user
                    ];
                    $dacController = new DeleteAccountController($dac_data);
                    $dacView = new DeleteAccountView($dacController);
                    $response[C::KEY_MESSAGE] = $dacView->getMessage();
                    if($dacView->isDone()) $response[C::KEY_DONE] = true;
                    else{
                        $errnoT = $dacController->getToken()->getErrno();
                        if($errnoT == Te::TOKENEXPIRED){
                            $response[C::KEY_EXPIRED] = true;
                            $response[C::KEY_MESSAGE] = Te::TOKENEXPIRED_MSG;
                        }
                    }
                    $response[C::KEY_CODE] = $dacView->getResponseCode();
                }catch(Exception $e){
                    $response[C::KEY_CODE] = 500;
                    $response[C::KEY_MESSAGE] = C::ACCOUNTDELETE_ERROR;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
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