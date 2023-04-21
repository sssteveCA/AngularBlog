<?php

require_once('../../../../vendor/autoload.php');

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Classes\Subscribe\VerifyView;
use Dotenv\Dotenv;

$response = [
    C::KEY_DONE => false, C::KEY_MESSAGE => ''
];

if(isset($_REQUEST['emailVerif']) && $_REQUEST['emailVerif'] != ''){
    if(preg_match(User::$regex['emailVerif'],$_REQUEST['emailVerif'])){  
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../");
        $dotenv->safeLoad();   
        try{
            $emailVerif = $_REQUEST['emailVerif'];
            $data = array('emailVerif' => $emailVerif);
            $user = new User($data);
            $vc = new VerifyController($user);
            $vv = new VerifyView($vc);
            $response[C::KEY_MESSAGE] = $vv->getMessage();
            switch($response[C::KEY_MESSAGE]){
                case C::ACTIVATION_OK:
                    http_response_code(200);
                    $response['status'] = 1;
                    break;
                case C::ACTIVATION_INVALID_CODE:
                    http_response_code(400);
                    $response['status'] = -1;
                    break;
                default:
                    http_response_code(500);
                    $response['status'] = -2;
                    break;
            }
        }
        catch(Exception $e){
            http_response_code(500);
            $response['status'] = -2;
        }
    }//if(preg_match(User::$regex['emailVerif'],$_REQUEST['emailVerif'])){
}//if(isset($_REQUEST['emailVerif']) && $_REQUEST['emailVerif'] != ''){
else{
    http_response_code(400);
    $response['status'] = 0;
} 

echo json_encode($response);
?>