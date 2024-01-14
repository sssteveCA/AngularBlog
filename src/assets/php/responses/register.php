<?php

namespace AngularBlog\Responses;

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
                    $dotenv->load();
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
                        if($rc->getErrno() == 0){
                            $response[C::KEY_DONE] = true;
                            $response[C::KEY_CODE] = 200;
                        }
                        $response[C::KEY_MESSAGE] = $rv->getMessage();
                        $response[C::KEY_CODE] =  $rv->getResponseCode();
                    }
                    catch(Exception $e){
                        $response[C::KEY_CODE] = 500;
                        $response[C::KEY_MESSAGE] = C::REG_ERROR;
                    }
                }
                else{
                    $response[C::KEY_CODE] = 400;
                    $response[C::KEY_MESSAGE] = C::ERROR_CONFIRM_PASSWORD_DIFFERENT;
                }
            }
            else{
                $response[C::KEY_CODE] = 400;
                $response[C::KEY_MESSAGE] = "La password ha un formato non valido";
            }
        }
        else{
            $response[C::KEY_CODE] =  400;
            $response[C::KEY_MESSAGE] = C::FILL_ALL_FIELDS;
        }
        return $response;
    }
}

?>