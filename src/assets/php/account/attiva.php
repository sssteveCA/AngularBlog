<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: *');

require_once("../config.php");
require_once("../class/bloguser.php");

$response = array();
$response['done'] = false;
$response['status'] = 0; //no code in URL

$regex = '/^[a-z0-9]{64}$/i';
    if(isset($_REQUEST['emailVerif']) && preg_match($regex,$_REQUEST['emailVerif'])){
        $dati = array();
        $dati['campo'] = 'codAut';
        $dati['emailVerif'] = $_REQUEST['emailVerif'];
        $bUser = new BlogUser($dati);
        $codAut = $bUser->getEmailVerif();
        $activate = $bUser->attiva();
        $errno = $bUser->getErrno();
        //account attivato
        if($errno == 0){
            $response['msg'] = 'L\' account è stato attivato';
            $response['status'] = 1;
            $response['done'] = true;
        }
        //account non attivato
        else{
            $response['msg'] = 'Account non attivato. Codice '.$errno;
            switch($errno){
                case BLOGUSER_ACCOUNTNOTACTIVATED:
                    $response['status'] = -1; //invalid email verification code
                    break;
                case BLOGUSER_DATANOTSET:
                default:
                    $response['status'] = 0;
                    break;     
            }
        }//else di if($errno == 0){
        $response['queries'] = $bUser->getQueries();
    }//if(isset($_REQUEST['codAut']) && preg_match($regex,$_REQUEST['codAut'])) 

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>