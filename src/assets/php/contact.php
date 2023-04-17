<?php

require_once("../../../vendor/autoload.php");

use AngularBlog\Classes\Contact\ContactController;
use AngularBlog\Classes\Contact\ContactView;
use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = [ C::KEY_DONE => false, C::KEY_MESSAGE => '' ];

if(isset($post['email'],$post['subject'],$post['message']) 
&& $post['email'] != '' && $post['subject'] != '' && $post['message'] != ''){
    if(preg_match(EMAILREGEX,$post['email'])){
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../");
        $dotenv->safeLoad();
        $email = $post['email'];
        $subject = $post['subject'];
        $message = $post['message'];
        try{
            $ccData = [
                'fromEmail' => $post['email'], 'toEmail' => C::ADMINMAIL, 
                'subject' => $post['subject'], 'message' => $post['message']
            ];
            $cc = new ContactController($ccData);
            $cv = new ContactView($cc);
            if($cv->isDone()) $response[C::KEY_DONE] = true;
            $response[C::KEY_MESSAGE] = $cv->getMessage();
            http_response_code($cv->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            $response[C::KEY_MESSAGE] = C::CONTACT_ERROR;
        }
    }//if(preg_match($emailRegex,$post['email'])){
    else
        $response[C::KEY_MESSAGE] = "L'indirizzo email inserito non è valido";
}
else{
    $response[C::KEY_MESSAGE] = 'Inserisci tutti i dati richiesti per continuare';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>