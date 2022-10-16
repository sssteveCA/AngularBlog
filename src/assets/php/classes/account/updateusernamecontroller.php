<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UpdateUsernameControllerErrors as Uuce;


class UpdateUsernameController implements Uuce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        $this->checkValues($data);
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

    private function checkValues(array $data){
        if(!isset($data['token']))throw new NoTokenInstanceException(Uuce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user']))throw new NoUserInstanceException(Uuce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Uuce::TOKENTYPEMISMATCH_EXC);
        if(!$data['token'] instanceof User)throw new UserTypeMismatchException(Uuce::USERTYPEMISMATCH_EXC);
    }

    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        return false;
    }

}
?>