<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UpdateUsernameControllerErrors as Uuce;


class UpdateUsernameController implements Uuce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        
    }

    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
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