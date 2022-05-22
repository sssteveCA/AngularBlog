<?php

require_once("../cors.php");
require_once("../config.php");
require_once("../interfaces/constants.php");
require_once("../interfaces/bloguser_errors.php");
require_once("../vendor/autoload.php");
require_once("../class/bloguser.php");

use AngularBlog\Classes\BlogUser;
use AngularBlog\Interfaces\BlogUserErrors as Bue;

$response = array();
$response['done'] = false;
$response['status'] = 0; //no code in URL

$regex = '/^[a-z0-9]{64}$/i';
    if(isset($_REQUEST['emailVerif']) && $_REQUEST['emailVerif'] != ''){
        $response['emailVerif'] = $_REQUEST['emailVerif'];
        if(preg_match($regex,$_REQUEST['emailVerif'])){
            $dati = array();
            $dati['field'] = 'codAut';
            $dati['emailVerif'] = $_REQUEST['emailVerif'];
            $bUser = new BlogUser($dati);
            $codAut = $bUser->getEmailVerif();
            $activate = $bUser->attiva();
            $errno = $bUser->getErrno();
            //account attivato
            if($errno == 0){
                //$response['msg'] = 'L\' account è stato attivato';
                $response['status'] = 1;
                $response['done'] = true;
            }
            //account non attivato
            else{
                //$response['msg'] = 'Account non attivato. Codice '.$errno;
                switch($errno){
                    case Bue::ACCOUNTNOTACTIVATED:
                        $response['status'] = -1; //invalid email verification code
                        break;
                    case Bue::DATANOTSET:
                    default:
                        $response['status'] = 0;
                        break;     
                }
            }//else di if($errno == 0){
            //$response['queries'] = $bUser->getQueries();
        }//if(preg_match($regex,$_REQUEST['emailVerif'])){
        else{
            $response['status'] = -1;
        }
    }//if(isset($_REQUEST['codAut']) && preg_match($rege
    else{
        $response['status'] = 0;
    }

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>