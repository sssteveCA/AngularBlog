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
        if(!$rc)throw new \Exception(Rve::NOREGISTRATIONCONTROLLERINSTANCE_EXC);
        $this->rc = $rc;
        $this->message = $this->rc->getResponse();
    }

    public function getMessage(){return $this->message;}
}
?>