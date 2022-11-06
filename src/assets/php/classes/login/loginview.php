<?php

namespace AngularBlog\Classes\Login;

use AngularBlog\Interfaces\Login\LoginViewErrors as Lve;
use AngularBlog\Classes\Login\LoginController;
use AngularBlog\Traits\MessageTrait;

class LoginView implements Lve{

    use MessageTrait;

    private ?LoginController $lc;
    private bool $loginOk = false; //If it's true don't show a message and redirect to personal account area

    public function __construct(?LoginController $lc)
    {
        if(!$lc)throw new \Exception(Lve::NOLOGINCONTROLLERINSTANCE_EXC);
        $this->lc = $lc;
        $this->response_code = $this->lc->getResponseCode();
        $errnoLc = $this->lc->getErrno();
        if($errnoLc == 0)
            $this->loginOk = true;
		else
			$this->message = $this->lc->getResponse();
        
    }

    public function isLogged(){return $this->loginOk;}
}
?>