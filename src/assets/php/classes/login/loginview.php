<?php

namespace AngularBlog\Classes\Login;

use AngularBlog\Interfaces\Login\LoginViewErrors as Lve;
use AngularBlog\Classes\Login\LoginController;

class LoginView implements Lve{

    private ?LoginController $lc;
    private bool $loginOk = false; //If it's true don't show a message and redirect to personal account area
    private string $message;

    public function __construct(?LoginController $lc)
    {
        if(!$lc)throw new \Exception(Lve::NOLOGINCONTROLLERINSTANCE_EXC);
        $this->lc = $lc;
        $errnoLc = $this->lc->getErrno();
        if($errnoLc == 0)
            $this->loginOk = true;
        $this->message = $this->lc->getResponse();
        
    }

    public function getMessage(){return $this->message;}
    public function isLogged(){return $this->loginOk;}
}
?>