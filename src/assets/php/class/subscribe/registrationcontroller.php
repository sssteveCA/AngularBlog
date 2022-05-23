<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Subscribe\RegistrationControllerErrors as Rce;
use AngularBlog\Interfaces\UserErrors as Ue;
use AngularBlog\Classes\User;

//This class add subscriber data to DB and send activation email to user
class RegistrationController implements Rce,Ue,C{

    private ?User $user; //User object with data to store in DB
    private ?string $headers = null; //Email activation headers
    private ?string $message = null; //Email activation body message
    private string $response = ""; //Response message
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?User $user)
    {
        $this->user = $user;
        if(!$this->user)throw new \Exception(Rce::NOUSERINSTANCE_EXC);
        $reg = $this->registration();
        if($reg){
            //Send email if data are added to DB
            $this->sendEmail();
        }
        $this->setResponse();
    }

    public function getUser(){return $this->user;}
    public function getResponse(){return $this->response;}
    public function getErrno(): int{return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Rce::MAILNOTSENT:
                $this->error = Rce::MAILNOTSENT_MSG;
                break;
            case Rce::FROMUSER:
                $this->error = Rce::FROMUSER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //store account registration values in DB
    private function registration(): bool{
        $this->errno = 0;
        $registration = false;
        $create = $this->user->user_create();
        if($create){
            //Data added to DB
            $registration = true;
        }//if($create){
        else $this->errno = Rce::FROMUSER;
        return $registration;
    }

    //Send activation email to user
    private function sendEmail(): bool{
        $this->errno = 0;
        $email = $this->user->getEmail();
        $this->setHeaders();
        $this->setMessage();
        $send = @mail($email,C::EMAIL_ACTIVATION_SUBJECT,$this->message,$this->headers);
        if(!$send) $this->errno = Rce::MAILNOTSENT; //email non inviata
        return $send;
    }

    //Set the email headers
    private function setHeaders(){
        //mail headers
$this->headers = <<<HEADER
From: Admin <noreply@localhost.lan>
Reply-to: <noreply@localhost.lan>
Content-type: text/html
MIME-Version: 1.0
HEADER;
    }

    //Set email message
    private function setMessage(){
        $indAtt = C::REG_SUBSCRIBE_LINK;
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

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = C::EMAIL_ACCOUNT_CREATED;
                break;
            case Rce::MAILNOTSENT:
                $this->response = C::EMAIL_ERROR;
                break;
            case Rce::FROMUSER:
                $errnoU = $this->user->getErrno();
                switch($errnoU){
                    case Ue::INVALIDDATAFORMAT:
                        $this->response = Ue::INVALIDDATAFORMAT_MSG;
                        break;
                    default:
                        $this->response = C::REG_ERROR;
                        break;
                }
                break;
            default:
                $this->error = C::REG_ERROR;
                break;
        }
    }


}
?>