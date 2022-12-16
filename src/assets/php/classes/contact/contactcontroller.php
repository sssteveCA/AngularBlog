<?php

namespace AngularBlog\Classes\Contact;

use AngularBlog\Traits\ErrorTrait;

class ContactController{

    use ErrorTrait;

    private string $email;
    private string $subject;
    private string $message;

    public function __construct(array $data)
    {
        
    }

    public function getEmail(){ return $this->email; }
    public function getSubject(){ return $this->subject; }
    public function getMessage(){ return $this->message; }
}
?>