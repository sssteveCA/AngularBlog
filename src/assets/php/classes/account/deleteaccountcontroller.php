<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
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

    }
   
    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
}
?>