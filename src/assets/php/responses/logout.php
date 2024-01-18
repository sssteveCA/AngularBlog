<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Logout\LogoutView;
use AngularBlog\Classes\Logout\LogoutController;
use Dotenv\Dotenv;

/**
 * JSON response for account logout GET route
 */
class Logout{

    public static function content(array $params): array{
        $response = [ C::KEY_CODE => 200, C::KEY_MESSAGE => '',C::KEY_DONE => false ];
        $headers = $params['headers'];
        if(isset($headers[C::KEY_AUTH]) && $headers[C::KEY_AUTH] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $token_key = $headers[C::KEY_AUTH];
                $filter = ['token_key' => $token_key];
                $token = new Token();
                $get = $token->token_get($filter);
                if($get){
                    //Found in DB a collection with token key passed from client
                    $logoutController = new LogoutController($token);
                    $logoutView = new LogoutView($logoutController);
                    $logout = $logoutView->isLogout();
                    if($logout){
                        //Server side logout done
                        $response[C::KEY_DONE] = true;
                    }
                    else{
                        $response[C::KEY_MESSAGE] = $logoutView->getMessage();
                    }
                    $response[C::KEY_CODE] = $logoutView->getResponseCode();
                }
                else{
                    $response[C::KEY_CODE] = 404;
                    $response[C::KEY_MESSAGE] = C::LOGOUT_ERROR_USERNOTFOUND;
                }
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
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