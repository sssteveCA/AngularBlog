<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Account\GetUserInfoControllerErrors as Guice;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;

/**
 * Used to get the information about the logged user
 */
class GetUserInfoController implements Guice{

    use ErrorTrait, ResponseMultipleTrait;

    private ?Token $token;
    private ?User $user;
    private string $email;
    private string $name;
    private string $surname;
    private string $username;

    public function __construct(array $data){

    }
    
    public function getToken(){ return $this->token; }
    public function getEmail(){ return $this->email; }
    public function getName(){ return $this->name; }
    public function getSurname(){ return $this->surname; }
    public function getUsername(){ return $this->username; }

}
?>