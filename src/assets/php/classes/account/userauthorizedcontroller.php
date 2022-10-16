<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UserAuthorizedControllerErrors as Uace;

/**
 * Check if user is authorized to manage the account
 */
class UserAuthorizedController implements Uace{
    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        $this->checkVariables($data);
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            
        }//if($tokenOk){
    }

    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Uace::TOKEN_NOTFOUND:
                $this->error = Uace::TOKEN_NOTFOUND_MSG;
                break;
            case Uace::FROM_TOKEN:
                $this->error = Uace::FROM_TOKEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function checkVariables(array $data){
        if(!isset($data['token']))throw new NoTokenInstanceException(Uace::NOTOKENINSTANCE_EXC);
        if(!isset($data['user']))throw new NoUserInstanceException(Uace::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Uace::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User)throw new UserTypeMismatchException(Uace::USERTYPEMISMATCH_EXC);
    }

    private function getTokenByKey(): bool{
        $this->errno = 0;
        $key = $this->token->getTokenKey();
        $data = ['token_key' => $key];
        $token_got = $this->token->token_get($data);
        if($token_got){
            $this->token->expireControl();
            if(!$this->token->isExpired())
                return true;
            else
                $this->errno = Uace::FROM_TOKEN;
        }
        else
            $this->errno = Uace::TOKEN_NOTFOUND;
        return false;
    }
}
?>