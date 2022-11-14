<?php

use Dotenv\Dotenv;

require_once("cors.php");
//require_once("../../../config.php");
require_once("../../../vendor/autoload.php");
require_once("config.php");

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = array();
$response['done'] = false;

if(isset($post['email'],$post['subject'],$post['message']) 
&& $post['email'] != '' && $post['subject'] != '' && $post['message'] != ''){
    if(preg_match(EMAILREGEX,$post['email'])){
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../");
        $dotenv->safeLoad();
        $email = $post['email'];
        $subject = $post['subject'];
        $message = $post['message'];
        $htmlMail = htmlMailContact($message);
        $send = @mail(ADMINEMAIL,$subject,$htmlMail,$headers);
        if($send){
            $response['done'] = true;
            $response['msg'] = 'Il messaggio è stato inviato. Sarai ricontattato il prima possibile';
        }
        else
            $response['msg'] = "C'è stato un'errore durante l'invio del messaggio";
    }//if(preg_match($emailRegex,$post['email'])){
    else
        $response['msg'] = "L'indirizzo email inserito non è valido";
}
else{
    $response['msg'] = 'Inserisci tutti i dati richiesti per continuare';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>