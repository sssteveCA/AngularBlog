<?php

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;

/**
 * Used to get name and the surname of the logged user
 */
class GetNamesController{

    use ErrorTrait; 
    private ?Token $token;
    private ?User $user;
    private string $username;

    public function __construct(array $data){

    }

    public function getToken(){ return $this->token; }
}

?>