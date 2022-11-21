<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;
use AngularBlog\Interfaces\Account\GetNamesControllerErrors as Gnce;

/**
 * Used to get name and the surname of the logged user
 */
class GetNamesController{

    use ErrorTrait, ResponseMultipleTrait; 

    private ?Token $token;
    private ?User $user;
    private string $username;

    public function __construct(array $data){

    }

    public function getToken(){ return $this->token; }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Gnce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Gnce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Gnce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Gnce::USERTYPEMISMATCH_EXC);
    }
}

?>