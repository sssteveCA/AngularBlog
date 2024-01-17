<?php

namespace AngularBlog\Responses;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Classes\Subscribe\VerifyView;
use Dotenv\Dotenv;

/**
 * JSON response for account activation GET route
 */
class Activate{

    public static function content(array $params): array{
        $response = [
            C::KEY_CODE => 200, C::KEY_DONE => false, C::KEY_MESSAGE => ''
        ];
        $get = $params['get'];
        if(isset($get['emailVerif']) && $get['emailVerif'] != ''){
            if(preg_match(User::$regex['emailVerif'],$get['emailVerif'])){
                try{
                    $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
                    $dotenv->load();
                    $emailVerif = $get['emailVerif'];
                    $data = array('emailVerif' => $emailVerif);
                    $user = new User($data);
                    $vc = new VerifyController($user);
                    $vv = new VerifyView($vc);
                    $response[C::KEY_MESSAGE] = $vv->getMessage();
                    switch($response[C::KEY_MESSAGE]){
                        case C::ACTIVATION_OK:
                            $response[C::KEY_DONE] = true;
                            $response[C::KEY_CODE] = 200;
                            $response['status'] = 1;
                            break;
                        case C::ACTIVATION_INVALID_CODE:
                            $response[C::KEY_CODE] = 400;
                            $response['status'] = -1;
                            break;
                        default:
                            $response[C::KEY_CODE] = 500;
                            $response['status'] = -2;
                            break;
                    }
                }catch(Exception $e){
                    $response[C::KEY_CODE] = 500;
                    $response['status'] = -2;
                }
            }
        }
        else{
            $response[C::KEY_CODE] = 400;
            $response['status'] = 0;
        }
        return $response;
        
    }
}

?>