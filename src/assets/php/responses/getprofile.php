<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Account\GetUserInfoControllerErrors as Guice;
use AngularBlog\Classes\Account\GetUserInfoController;
use AngularBlog\Classes\Account\GetUserInfoView;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;
use Exception;

/**
 * JSON response for get profile info GET route
 */
class GetProfile{
    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => "", C::KEY_DATA => []
        ];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ""){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $token_data = ["token_key" => $headers[C::KEY_AUTH]];
                $token = new Token($token_data);
                $user = new User([]);
                $guic_data = [ 'token' => $token, 'user' => $user ];
                $guic = new GetUserInfoController($guic_data);
                $guiv = new GetUserInfoView($guic);
                if($guiv->isDone()){
                    $data = $guiv->getMessageArray();
                    $response = [
                        C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_EXPIRED => false, C::KEY_MESSAGE => "", C::KEY_DATA => $data
                    ];
                }
                else{
                    if($guic->getErrno() == Guice::FROM_TOKEN){
                        if($guic->getToken()->getErrno() == Te::TOKENEXPIRED)
                            $response[C::KEY_EXPIRED] = true;
                    }
                }
                $response[C::KEY_CODE] = $guiv->getResponseCode();
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $error = $e->getMessage();
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response[C::KEY_MESSAGE] = C::ERROR_TOKEN_MISSED;
        }
        return $response;
    }
}
?>