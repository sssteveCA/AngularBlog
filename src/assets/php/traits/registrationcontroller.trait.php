<?php

namespace AngularBlog\Traits;

use AngularBlog\Classes\Email\EmailManager;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Subscribe\RegistrationControllerErrors as Rce;


trait RegistrationControllerTrait{

    /**
     * Send activation email to user
     */
    private function sendEmail(): bool{
        $this->errno = 0;
        $email = $this->user->getEmail();
        $this->setMessage();
        $emData =  [C::ADMINMAIL, $_ENV["MAIL_USERNAME"],
            "to" => $email, "subject" => C::EMAIL_ACTIVATION_SUBJECT, "body" => $this->message
        ];
        $em = new EmailManager($emData);
        if($em->getErrno() != 0){
            $this->error = Rce::MAILNOTSENT;
            return false;
        }
        return true;
    }

       /**
     * Set the mail message
     */
    private function setMessage(){
        $indAtt = $_ENV['ANGULAR_MAIN_URL']."/attiva";
        $emailVerif = $this->user->getEmailVerif();
        $codIndAtt = $indAtt.'?emailVerif='.$emailVerif;
        $this->message = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Attivazione account</title>
        <meta charset="utf-8">
        <style>
            body{
                display: flex;
                justify-content: center;
            }
            div#linkAtt{
                padding: 10px;
                background-color: orange;
                font-size: 20px;
            }
        </style>
    <head>
    <body>
        <div id="linkAtt">
Completa la registrazione facendo click sul link sottostante:
<p><a href="{$codIndAtt}">{$codIndAtt}</a></p>
oppure vai all'indirizzo <p><a href="{$indAtt}">{$indAtt}</a></p> e incolla il seguente codice: 
<p>{$emailVerif}</p>
        </div>
    </body>
</html>
HTML;
    }

}
?>