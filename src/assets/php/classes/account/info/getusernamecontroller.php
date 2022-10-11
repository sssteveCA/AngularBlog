<?php

namespace AngularBlog\Classes\Account\Info;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Account\Info\GetUsernameControllerErrors as Guce;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Constants as C;

class GetUsernameController implements Guce {
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private string $username;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Guce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Guce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Guce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Guce::USERTYPEMISMATCH_EXC);
    }

    private function getUsername(): bool{
        $this->errno = 0;
        $token_key = $this->token->getTokenKey();
        $token_get = $this->token->token_get(['token_key' => $token_key]);
        if($this->token->getErrno() != 0) return false;
        $user_id = $this->token->getUserId();
        $user_get = $this->user->user_get(['_id' => $user_id]);
        if($this->user->getErrno() != 0)return false;
        $this->username = $this->user->getUsername();
        return true;
    }
}
?>