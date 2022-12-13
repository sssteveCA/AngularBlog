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
use MongoDB\BSON\ObjectId;

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
        $auth = $this->checkAuthorization();
        if($auth)
            $this->delete_account();
    }
   
    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Dace::CURRENT_PASSWORD_WRONG:
                $this->error = Dace::CURRENT_PASSWORD_WRONG_MSG;
                break;
            case Dace::DELETE_USER:
                $this->error = Dace::DELETE_USER_MSG;
                break;
            case Dace::FROM_USERAUTHORIZEDCONTROLLER:
                $this->error = Dace::FROM_USERAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

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

    /**
     * Check if the user is authorized to delete the account
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        $this->uac = new UserAuthorizedController([
            'token' => $this->token, 'user' => $this->uac_user
        ]);
        $uacErrno = $this->uac->getErrno();
        if($uacErrno == 0) return true;
        $this->errno = Dace::FROM_USERAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Delete the current logged account from database
     */
    private function delete_account(): bool{
        $this->errno = 0;
        $user_password_hash = $this->uac_user->getPasswordHash();
        if(password_verify($this->password,$user_password_hash)){
            $user_id = $this->token->getUserId();
            $filter = ['_id' => new ObjectId($user_id)];
            $account_delete = $this->user->user_delete($filter);
            if($account_delete) return true;
            else $this->errno = Dace::DELETE_USER;
        }//if(password_verify($this->password,$user_password_hash)){
        else $this->errno = Dace::CURRENT_PASSWORD_WRONG;
        return false;
    }
}
?>