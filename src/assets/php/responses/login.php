<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Login\LoginController;
use AngularBlog\Classes\Login\LoginView;
use Dotenv\Dotenv;
use Exception;

/**
 * JSON response for login POST request
 */
class Login{

    public static function content(array $params): array{
        $post = $params['post'];
        $response = [ C::KEY_DONE => false, C::KEY_CODE => 200 ];
        if(isset($post['username'],$post['password']) && $post['username'] != '' && $post['password'] != ''){
            try{
                $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                $dotenv->load();
                $data = [
                    'username' => $post['username'],
                    'password' => $post['password']
                ];
                $user = new User($data);
                $loginController = new LoginController($user);
                $loginView = new LoginView($loginController);
                $logged = $loginView->isLogged();
                if($logged){
                    //Correct credentials and account activated
                    $token = $loginController->getToken();
                    $response['username'] = $token->getUsername();
                    $response['token_key'] = $token->getTokenKey();
                    $response[C::KEY_DONE] = true;
                }//if($logged){
                else{
                    $response[C::KEY_MESSAGE] = $loginView->getMessage();
                }
                $response['error'] = $loginController->getError();
                $response[C::KEY_CODE] = $loginView->getResponseCode();
                    
            }catch(Exception $e){
                $response[C::KEY_CODE] = 500;
                $response[C::KEY_MESSAGE] = C::LOGIN_ERROR;
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