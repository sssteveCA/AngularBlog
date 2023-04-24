<?php

namespace AngularBlog\Classes\Email;

use AngularBlog\Exceptions\NotSettedException;
use AngularBlog\Traits\EmailManagerTrait;
use AngularBlog\Traits\ErrorTrait;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use AngularBlog\Interfaces\Email\EmailManagerErrors as Eme;

class EmailManager extends PHPMailer implements Eme{

    use EmailManagerTrait, ErrorTrait;

    /**
     * Sender email
     */
    private ?string $fromEmail;

    /**
     * Receiver email
     */
    private string $toEmail;

    /**
     * The subject of the mail
     */
    private string $subject;

    /**
     * The content of the mail
     */
    private string $body;

    public function __construct(array $data)
    {
        if(!$this->checkExists($data))
            throw new NotSettedException("");
        $this->assignValues($data);
        $this->setServerSettings($data);
        $this->setEncoding();
        $this->sendMessage();
    }

    public function getFromEmail(){ return $this->fromEmail; }
    public function getToEmail(){ return $this->toEmail; }
    public function getSubject(){ return $this->subject; }
    public function getBody(){ return $this->body; }

    private function sendMessage(){
        $this->errno = 0;
        try{
            if(isset($this->fromEmail))
                $this->setFrom($this->fromEmail,$this->fromEmail);
            $this->addAddress($this->toEmail,$this->toEmail);
            $this->Subject = $this->subject;
            $this->Body = $this->body;
            $this->AltBody = $this->body;
            $this->send();
        }catch(Exception $e){
            $this->errno = Eme::ERR_EMAIL_SEND;
        }
    }
}
?>