<?php

namespace AngularBlog\Classes\Logout;

use AngularBlog\Interfaces\Logout\LogoutViewErrors as Love;
use AngularBlog\Classes\Logout\LogoutController;
use AngularBlog\Traits\MessageTrait;

class LogoutView implements Love{

    use MessageTrait;

    private ?LogoutController $loc;
    private bool $logoutOk = false; //If it's true don't show a message and redirect to homepage

    public function __construct(?LogoutController $loc)
    {
        if(!$loc)throw new \Exception(Love::NOLOGOUTCONTROLLERINSTANCE_EXC);
        $this->loc = $loc;
        $this->response_code = $this->loc->getResponseCode();
        $errnoLoc = $this->loc->getErrno();
        if($errnoLoc == 0)
            $this->logoutOk = true;
        else
            $this->message = $this->loc->getResponse();
    }

    public function isLogout(){return $this->logoutOk;}
}
?>