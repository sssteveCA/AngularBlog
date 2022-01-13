<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

require_once("config.php");
require_once("class/bloguser.php");

$response = array();
$response['post'] = $_POST;
$response['done'] = false;

if(isset($_POST['nome'],$_POST['cognome'],$_POST['username'],$_POST['email'],$_POST['password'],$_POST['confPwd'])){
    if($_POST['password'] == $_POST['confPwd']){
        if(preg_match(BlogUser::$regex['password'],$_POST['password'])){
            try{
                $dati = array(
                    'nome' => $_POST['nome'],
                    'cognome' => $_POST['cognome'],
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                );
                $bUser = new BlogUser($dati);
                $reg = $bUser->registration();
                if($reg){
                    $response['msg'] = 'Account creato con successo. Per completare la registrazione accedi alla tua casella di posta';
                }
                else{
                    $errno = $bUser->getErrno();
                    $response['msg'] = 'Errore durante la registrazione dell\' account. Codice '.$errno;
                }
                $response['queries'] = $bUser->getQueries();
            }catch(Exception $e){
                $response['msg'] = $e->getMessage();
            }
        }//if(preg_match(BlogUser::$regex['password'],$_POST['password'])){
    }//if($_POST['password'] == $_POST['confPwd']){
    else{
        $response['msg'] = 'Le due password non coincidono';
    }
}
else{
    $response['msg'] = 'Inserisci tutti i dati richiesti per continuare';
}

echo json_encode($response,JSON_UNESCAPED_UNICODE);
?>