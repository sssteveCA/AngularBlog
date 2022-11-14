<?php

require_once("../cors.php");
require_once('../../../../vendor/autoload.php');
require_once("../config.php");
require_once('../interfaces/constants.php');
require_once('../interfaces/exception_messages.php');
require_once('../interfaces/from_errors.php');
require_once('../interfaces/model_errors.php');
require_once('../interfaces/user_errors.php');
require_once('../interfaces/subscribe/verifycontroller_errors.php');
require_once('../interfaces/subscribe/verifyview_errors.php');
require_once("../traits/error.trait.php");
require_once("../traits/message.trait.php");
require_once("../traits/response.trait.php");
require_once('../classes/model.php');
require_once('../classes/user.php');
require_once('../classes/subscribe/verifycontroller.php');
require_once('../classes/subscribe/verifyview.php');

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Classes\Subscribe\VerifyView;
use Dotenv\Dotenv;

$response = array();
$response['msg'] = '';
$response['done'] = false;

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
            $response['msg'] = $vv->getMessage();
            file_put_contents(C::FILE_LOG,"Msg => \r\n",FILE_APPEND);
            file_put_contents(C::FILE_LOG,$response['msg']."\r\n",FILE_APPEND);
            switch($response['msg']){
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
            file_put_contents(C::FILE_LOG,$e."\r\n",FILE_APPEND);
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