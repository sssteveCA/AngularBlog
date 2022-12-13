<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\MissingValuesException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Interfaces\Account\DeleteAccountControllerErrors as Dace;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class DeleteAccountController implements Dace{
    use ErrorTrait, ResponseTrait;

    private string $conf_password;
    private string $password;
    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data){
        $this->checkValues($data);
    }
   
    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}

    private function checkValues(array $data){
        if(!isset($data['conf_password'],$data['password']))throw new MissingValuesException(Dace::MISSINGVALUES_EXC);
        if(!isset($data['token'])) throw new NoTokenInstanceException(Dace::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Dace::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Dace::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Dace::USERTYPEMISMATCH_EXC);
        $this->conf_password = $data['conf_password'];
        $this->password = $data['password'];
        $this->token = $data['token'];
        $this->user = $data['user'];
    }
}
?>