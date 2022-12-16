<?php

namespace AngularBlog\Classes\Email;

use AngularBlog\Traits\EmailManagerTrait;
use AngularBlog\Traits\ErrorTrait;
use PHPMailer\PHPMailer\PHPMailer;

class EmailManager extends PHPMailer{

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
        
    }

    public function getFromEmail(){ return $this->fromEmail; }
    public function getToEmail(){ return $this->toEmail; }
    public function getSubject(){ return $this->subject; }
    public function getBody(){ return $this->body; }
}
?>