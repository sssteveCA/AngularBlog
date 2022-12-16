<?php

require_once("cors.php");
require_once("interfaces/email/emailmanager_errors.php");
require_once("interfaces/contact/contactview_errors.php");
require_once("interfaces/contact/contactcontroller_errors.php");
require_once("interfaces/email/emailmanager_errors.php");
require_once("../../../vendor/autoload.php");
require_once("traits/emailmanager.trait.php");
require_once("traits/error.trait.php");
require_once("traits/message.trait.php");
require_once("traits/response.trait.php");
require_once("classes/email/emailmanager.php");
require_once("classes/contact/contactcontroller.php");
require_once("classes/contact/contactview.php");
require_once("config.php");

use AngularBlog\Classes\Contact\ContactController;
use AngularBlog\Classes\Contact\ContactView;
use Dotenv\Dotenv;
use AngularBlog\Interfaces\Constants as C;

$input = file_get_contents("php://input");
$post = json_decode($input,true);

$response = [ 'done' => false, 'msg' => '' ];

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
                'fromEmail' => $post['email'], 'toEmail' => $_ENV['EMAIL_USERNAME'], 
                'subject' => $post['subject'], 'message' => $post['message']
            ];
            $cc = new ContactController($ccData);
            $cv = new ContactView($cc);
            if($cv->isDone()) $response['done'] = true;
            $response['msg'] = $cv->getMessage();
            http_response_code($cv->getResponseCode());
        }catch(Exception $e){
            http_response_code(500);
            $response['msg'] = C::CONTACT_ERROR;
        }
    }//if(preg_match($emailRegex,$post['email'])){
    else
        $response['msg'] = "L'indirizzo email inserito non è valido";
}
else{
    $response['msg'] = 'Inserisci tutti i dati richiesti per continuare';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>