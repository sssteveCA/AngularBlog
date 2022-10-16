<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

/**
 * Check if user is authorized to manage the account
 */
class UserAuthorizedController{
    use ErrorTrait, ResponseTrait, AuthorizedTrait;

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