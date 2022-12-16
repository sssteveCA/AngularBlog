<?php

namespace AngularBlog\Classes\Contact;

use AngularBlog\Classes\Email\EmailManager;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Contact\ContactControllerErrors as Cce;
use AngularBlog\Interfaces\Constants as C;

class ContactController implements Cce{

    use ErrorTrait, ResponseTrait;

    /**
     * The email address of the user that sent the mail
     */
    private string $fromEmail;
    /**
     * The recipient address of the mail
     */
    private string $toEmail;
    /**
     * The subject of the mail
     */
    private string $subject;
    /**
     * The body message of the mail
     */
    private string $message;

    public function __construct(array $data)
    {
        $this->setValues($data);
        $this->sendMessage();
        $this->setResponse();
    }

    public function getFromEmail(){ return $this->fromEmail; }
    public function getToEmail(){ return $this->toEmail; }
    public function getSubject(){ return $this->subject; }
    public function getMessage(){ return $this->message; }
    public function getError(){
        switch($this->errno){
            case Cce::MAILNOTSENT:
                $this->error = Cce::MAILNOTSENT_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function setValues(array $data){
        $this->fromEmail = $data["fromEmail"];
        $this->toEmail = $data["toEmail"];
        $this->subject = $data["subject"];
        $this->message = $data["message"];
    }

    private function sendMessage(){
        $emData = [
            "from" => $this->fromEmail,
            "to" => $this->toEmail,
            "subject" => $this->subject,
            "body" => $this->message
        ];
        $em = new EmailManager($emData);
        if($em->getErrno() != 0){
            $this->errno = Cce::MAILNOTSENT;
        }
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = C::CONTACT_OK;
                break;
            case Cce::MAILNOTSENT:
            default:
                $this->response_code = 500;
                $this->response = C::CONTACT_ERROR;
                break;
        }
    }
}
?>