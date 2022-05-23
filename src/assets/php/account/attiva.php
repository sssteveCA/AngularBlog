<?php

require_once("../cors.php");
require_once("../config.php");
require_once('../interfaces/constants.php');
require_once('../interfaces/model_errors.php');
require_once('../interfaces/user_errors.php');
require_once('../interfaces/subscribe/verifycontroller_errors.php');
require_once('../interfaces/subscribe/verifyview_errors.php');
require_once('../class/model.php');
require_once('../class/user.php');
require_once('../class/subscribe/verifycontroller.php');
require_once('../class/subscribe/verifyview.php');

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\User;
use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Classes\Subscribe\VerifyView;

$response = array();
$response['msg'] = '';
$response['done'] = false;

if(isset($_REQUEST['emailVerif']) && $_REQUEST['emailVerif'] != ''){
    if(preg_match(User::$regex['emailVerif'],$_REQUEST['emailVerif'])){     
        try{
            $emailVerif = $_REQUEST['emailVerif'];
            $data = array('emailVerif' => $emailVerif);
            $user = new User($data);
            $vc = new VerifyController($user);
            $vv = new VerifyView($vc);
            $msg = $vv->getMessage();
            switch($msg){
                case C::ACTIVATION_OK:
                    $response['status'] = 0;
                    break;
                case C::ACTIVATION_INVALID_CODE:
                    $response['status'] = -1;
                    break;
                default:
                    $response['status'] = -2;
                    break;
            }
        }
        catch(Exception $e){
            $response['status'] = -2;
        }
    }//if(preg_match(User::$regex['emailVerif'],$_REQUEST['emailVerif'])){
}//if(isset($_REQUEST['emailVerif']) && $_REQUEST['emailVerif'] != ''){

echo json_encode($response);
?>