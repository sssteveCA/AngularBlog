<?php

namespace AngularBlog\Classes\Logout;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Logout\LogoutControllerErrors as Loce;
use AngularBlog\Classes\Token;

class LogoutController implements C,Loce{
    private ?Token $token;
    private ?string $response = "";
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?Token $token)
    {
        if(!$token)throw new \Exception(Loce::NOTOKENINSTANCE_EXC);
        $this->token = $token;
    }

    public function getToken(){return $this->token;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

}
?>