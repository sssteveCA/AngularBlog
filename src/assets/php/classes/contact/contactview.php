<?php

namespace AngularBlog\Classes\Contact;

use AngularBlog\Interfaces\Contact\ContactViewErrors as Cve;
use AngularBlog\Traits\MessageTrait;
use Exception;

class ContactView{
    use MessageTrait;

    private ?ContactController $cc;

    public function __construct(?ContactController $cc)
    {
        if(!$cc) throw new Exception(Cve::NOCONTACTCONTROLLERINSTANCE_EXC);
        $this->cc = $cc;
        $this->response_code = $this->cc->getResponseCode();
        $this->message = $this->cc->getResponse();
        $errnoCc = $this->cc->getErrno();
        if($errnoCc == 0)
            $this->done = true;
    }
}
?>