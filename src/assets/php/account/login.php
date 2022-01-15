<?php

require_once("../config.php");
require_once("../class/bloguser.php");

$response = array();
$response['done'] = false;
$response['post'] = $_POST;

if(isset($_POST['username'],$_POST['password']) && $_POST['username'] != '' && $_POST['password'] != ''){
    try{
        $dati = array(
            'username' => $_POST['username'],
            'password' => $_POST['password']
        );
        $blogUser = new BlogUser($dati);
        //try to login with data passed
        $blogUser->login();
        if($blogUser->isLogged()){
            //login success
            $response['msg'] = 'Login effettuato con successo';
        }//if($blogUser->isLogged()){
        else{
            //login failure
            $errno = $blogUser->getErrno();
            switch($errno){
                case BLOGUSER_NORESULT:
                    $response['msg'] = 'I dati inseriti non sono validi. Riprova';
                    break;
                case BLOGUSER_ACCOUNTNOTACTIVATED:
                    $response['msg'] = "Devi attivare l'account prima di accedere";
                    break;
                default:
                    $response['msg'] = UNKNOWN_ERROR;
                    break;
            }
        }
        
    }
    catch(Exception $e){
        $response['msg'] = UNKNOWN_ERROR;
    }
}//if(isset($_POST['username'],$_POST['password'])){
else{
    $response['msg'] = 'Inserisci i dati richiesti per continuare';
}

echo json_encode($response);
?>