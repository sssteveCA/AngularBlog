<?php

namespace AngularBlog\Responses;

require_once('../../../../vendor/autoload.php');

use AngularBlog\Classes\Subscribe\RegistrationController;
use AngularBlog\Classes\Subscribe\RegistrationView;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use Dotenv\Dotenv;

/**
 * JSON response for register POST request
 */
class Register{

    public static function content(array $params): array{
        $post = $params['post'];
        $response = [
            C::KEY_DONE => false, C::KEY_MESSAGE => ''
        ];
        if(isset($post['name'],$post['surname'],$post['username'],$post['email'],$post['password'],$post['confPwd'],$post['subscribed'])){
            if(preg_match(User::$regex['password'],$post['password'])){
                if($post['password'] == $post['confPwd']){
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->safeLoad();
                    try{
                        $data = array(
                            'name' => $post['name'],
                            'surname' => $post['surname'],
                            'username' => $post['username'],
                            'email' => $post['email'],
                            'password' => $post['password'],
                            'subscribed' => $post['subscribed'],
                        );
                        $user = new User($data);
                        $rc = new RegistrationController($user);
                        $rv = new RegistrationView($rc);
                        if($rc->getErrno() == 0)$response[C::KEY_DONE] = true;
                        $response[C::KEY_MESSAGE] = $rv->getMessage();
                        http_response_code($rv->getResponseCode());
                    }
                    catch(Exception $e){
                        http_response_code(500);
                        $response[C::KEY_MESSAGE] = C::REG_ERROR;
                    }
                }
                else{
                    $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
                }
            }
            else{
                http_response_code(400);
                $response[C::KEY_MESSAGE] = "La password ha un formato non valido";
            }
        }
        else{
            http_response_code(400);
            $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
        }
        return $response;
    }
}

?>