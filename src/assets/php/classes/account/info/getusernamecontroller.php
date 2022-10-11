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

class GetUsernameController implements Guce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
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
        return false;
    }
}
?>