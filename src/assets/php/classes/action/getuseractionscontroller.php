<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;
use AngularBlog\Interfaces\Action\GetUserActionControllerErrors as Guace;

class GetUserActionsController implements Guace{

    use ErrorTrait, ResponseMultipleTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        
    }

    public function getToken(){ return $this->token; }
    public function getUser(){ return $this->user; }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Guace::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Guace::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Guace::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Guace::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }
}
?>