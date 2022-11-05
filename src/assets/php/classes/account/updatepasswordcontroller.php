<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\MissingValuesException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UpdatePasswordControllerErrors as Upce;
use MongoDB\BSON\ObjectId;

class UpdatePasswordController implements Upce{
    use ErrorTrait, ResponseTrait;

    private string $current_password;
    private string $new_password;
    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    private function __construct(array $data)
    {
        $this->checkValues($data);
        $auth = $this->checkAuthorization();
    }

    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Upce::CURRENT_PASSWORD_WRONG:
                $this->error = Upce::CURRENT_PASSWORD_WRONG_MSG;
                break;
            case Upce::UPDATE_USER:
                $this->error = Upce::UPDATE_USER_MSG;
                break;
            case Upce::FROM_USERAUTHORIZEDCONTROLLER:
                $this->error = Upce::FROM_USERAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function checkValues(array $data){
        if(!isset($data['old_password'],$data['new_password']))throw new MissingValuesException(Upce::MISSINGVALUES_EXC);
        if(!isset($data['token'])) throw new NoTokenInstanceException(Upce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Upce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Upce::TOKENTYPEMISMATCH_EXC);
        if(!$data['token'] instanceof User) throw new UserTypeMismatchException(Upce::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    /**
     * Check if user is authorized to update the password
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        $this->uac = new UserAuthorizedController([
            'token' => $this->token, 'user' => $this->uac_user
        ]);
        $uacErrno = $this->uac->getErrno();
        if($uacErrno == 0) return true;
        $this->errno = Upce::FROM_USERAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Update the password of the logged account
     */
    private function update_password(): bool{
        $this->errno = 0;
        if(password_verify($this->current_password,$this->uac_user->getPassword())){
            $user_id = $this->token->getUserId();
            $filter = ['_id' => new ObjectId($user_id)];
            $new_password_hash = password_hash($this->new_password,PASSWORD_DEFAULT);
            $values = ['set' => [
                'password' => $new_password_hash
            ]];
            $password_update = $this->user->user_update($filter,$values);
            if($password_update) return true;
            else $this->errno = Upce::UPDATE_USER;
        }//if(password_verify($this->current_password,$this->uac_user->getPassword())){
        else $this->errno = Upce::CURRENT_PASSWORD_WRONG;
        return false;
    }
}
?>