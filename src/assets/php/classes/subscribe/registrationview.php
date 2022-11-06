<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Interfaces\Subscribe\RegistrationViewErrors as Rve;
use AngularBlog\Classes\Subscribe\RegistrationController;
use AngularBlog\Traits\MessageTrait;

//Registration status message class
class RegistrationView implements Rve{

    use MessageTrait;

    private ?RegistrationController $rc;

    public function __construct(?RegistrationController $rc)
    {
        if(!$rc)throw new \Exception(Rve::NOREGISTRATIONCONTROLLERINSTANCE_EXC);
        $this->rc = $rc;
        $this->response_code = $this->rc->getResponseCode();
        $this->message = $this->rc->getResponse();
    }

}
?>