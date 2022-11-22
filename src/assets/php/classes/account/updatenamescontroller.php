<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Account\UpdateNamesControllerErrors as Unce;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class UpdateNamesController implements Unce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data)
    {
        
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Unce::UPDATE_TOKEN:
                $this->errno = Unce::UPDATE_TOKEN_MSG;
                break;
            case Unce::UPDATE_USER:
                $this->error = Unce::UPDATE_USER_MSG;
                break;
            case Unce::FROM_USERAUTHORIZEDCONTROLLER:
                $this->error = Unce::FROM_USERAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }
}
?>