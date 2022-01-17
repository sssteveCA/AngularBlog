<?php

require_once("cors.php");
require_once("config.php");

$response = array();
$response['done'] = false;

if(isset($_POST['email'],$_POST['subject'],$_POST['message']) 
&& $_POST['email'] != '' && $_POST['subject'] != '' && $_POST['message'] != ''){
    if(preg_match(EMAILREGEX,$_POST['email'])){
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $htmlMail = htmlMailContact($message);
        $send = @mail(ADMINEMAIL,$subject,$htmlMail,$headers);
        if($send){
            $response['done'] = true;
            $response['msg'] = 'Il messaggio è stato inviato. Sarai ricontattato il prima possibile';
        }
        else
            $response['msg'] = "C'è stato un'errore durante l'invio del messaggio";
    }//if(preg_match($emailRegex,$_POST['email'])){
    else
        $response['msg'] = "L'indirizzo email inserito non è valido";
}
else{
    $response['msg'] = 'Inserisci tutti i dati richiesti per continuare';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>