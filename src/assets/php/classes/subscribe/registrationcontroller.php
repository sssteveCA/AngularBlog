<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\Email\EmailManager;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Subscribe\RegistrationControllerErrors as Rce;
use AngularBlog\Interfaces\UserErrors as Ue;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use Exception;

//This class add subscriber data to DB and send activation email to user
class RegistrationController implements Rce,Ue,C{

    use ErrorTrait, ResponseTrait;

    private ?User $user; //User object with data to store in DB
    private ?string $headers = null; //Email activation headers
    private ?string $message = null; //Email activation body message

    public function __construct(?User $user)
    {
        if(!$user)throw new NoUserInstanceException(Rce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $reg = $this->registration();
        if($reg){
            //Send email if data are added to DB
            $this->sendEmail();
        }
        $this->setResponse();
    }

    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Rce::MAILNOTSENT:
                $this->error = Rce::MAILNOTSENT_MSG;
                break;
            case Rce::FROM_USER:
                $this->error = Rce::FROM_USER_MSG;
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
        else $this->errno = Rce::FROM_USER;
        return $registration;
    }

    //Send activation email to user
    private function sendEmail(): bool{
        $this->errno = 0;
        $email = $this->user->getEmail();
        $this->setHeaders();
        $this->setMessage();
        $emData =  [
            "from" => $_ENV["MAIL_USERNAME"],
            "to" => $email, "subject" => C::EMAIL_ACTIVATION_SUBJECT, "body" => $this->message
        ];
        $em = new EmailManager($emData);
        if($em->getErrno() != 0){
            $this->error = Rce::MAILNOTSENT;
            return false;
        }
        return true;
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

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = C::EMAIL_ACCOUNT_CREATED;
                break;
            case Rce::MAILNOTSENT:
                $this->response_code = 500;
                $this->response = C::EMAIL_ERROR;
                break;
            case Rce::FROM_USER:
                $errnoU = $this->user->getErrno();
                switch($errnoU){
                    case Ue::INVALIDDATAFORMAT:
                        $this->response_code = 400;
                        $this->response = Ue::INVALIDDATAFORMAT_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::REG_ERROR;
                        break;
                }
                break;
            default:
                $this->response_code = 500;
                $this->error = C::REG_ERROR;
                break;
        }
    }


}
?>