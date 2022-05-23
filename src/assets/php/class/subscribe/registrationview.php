<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Interfaces\Subscribe\RegistrationViewErrors as Rve;
use AngularBlog\Classes\Subscribe\RegistrationController;

//Registration status message class
class RegistrationView implements Rve{
    private ?RegistrationController $rc;
    private string $message;

    public function __construct(?RegistrationController $rc)
    {
        $this->rc = $rc;
        if(!$this->rc)throw new \Exception(Rve::NOREGISTRATIONCONTROLLERINSTANCE_EXC);
        $this->message = $this->rc->getResponse();
    }

    public function getMessage(){return $this->message;}
}
?>