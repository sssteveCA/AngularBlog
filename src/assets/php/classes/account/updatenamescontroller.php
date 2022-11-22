<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Interfaces\Account\UpdateNamesControllerErrors as Unce;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use MongoDB\BSON\ObjectId;

class UpdateNamesController implements Unce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data)
    {
       $this->checkValues($data);
       $auth = $this->checkAuthorization();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
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

    private function checkValues(array $data){
        if(!isset($data['token']))throw new NoTokenInstanceException(Unce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user']))throw new NoUserInstanceException(Unce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Unce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User)throw new UserTypeMismatchException(Unce::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    /**
     * Check if user is authorized to update the name and the surname
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        $this->uac = new UserAuthorizedController([
            'token' => $this->token,
            'user' => $this->uac_user,
        ]);
        $uacErrno = $this->uac->getErrno();
        if($uacErrno == 0) return true;
        $this->errno = Unce::FROM_USERAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Update the name and the surname of the logged account
     */
    private function updateNames(): bool{
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $new_name = $this->user->getName();
        $new_surname = $this->user->getSurname();
        $filter = ['_id' => new ObjectId($user_id)];
        $values = ['$set' => [
            'name' => $new_name, 'surname' => $new_surname
        ]];
        $user_update = $this->user->user_update($filter,$values);
        if($user_update) return true;
        $this->errno = Unce::UPDATE_USER;
        return false;
    }


}
?>