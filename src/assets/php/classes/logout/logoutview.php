<?php

namespace AngularBlog\Classes\Logout;

use AngularBlog\Interfaces\Logout\LogoutViewErrors as Love;
use AngularBlog\Classes\Logout\LogoutController;

class LogoutView implements Love{
    private ?LogoutController $loc;
    private bool $logoutOk = false; //If it's true don't show a message and redirect to homepage
    private string $message = "";

    public function __construct(?LogoutController $loc)
    {
        if(!$loc)throw new \Exception(Love::NOLOGOUTCONTROLLERINSTANCE_EXC);
        $this->loc = $loc;
        $errnoLoc = $this->loc->getErrno();
        if($errnoLoc == 0)
            $this->logoutOk = true;
        else
            $this->message = $this->loc->getResponse();
    }

    public function getMessage(){return $this->message;}
    public function isLogout(){return $this->logoutOk;}
}
?>