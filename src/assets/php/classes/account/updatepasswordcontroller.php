<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UpdatePasswordControllerErrors as Upce;

class UpdatePasswordController implements Upce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    private function __construct(array $data)
    {
        
    }

    public function getToken(){return $this->token;}
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