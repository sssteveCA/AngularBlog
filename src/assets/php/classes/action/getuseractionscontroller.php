<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;

class GetUserActionsController{

    use ErrorTrait, ResponseMultipleTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        
    }

    public function getToken(){ return $this->token; }
    public function getUser(){ return $this->user; }
}
?>