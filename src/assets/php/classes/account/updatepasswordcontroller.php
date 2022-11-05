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
use AngularBlog\Interfaces\Account\UserAuthorizedControllerErrors as Uace;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Constants as C;
use MongoDB\BSON\ObjectId;

class UpdatePasswordController implements Upce{
    use ErrorTrait, ResponseTrait;

    private string $current_password;
    private string $new_password;
    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $auth = $this->checkAuthorization();
        if($auth)
            $this->update_password();
        $this->setResponse();
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
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Upce::USERTYPEMISMATCH_EXC);
        $this->new_password = $data['new_password'];
        $this->current_password = $data['old_password'];
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
        $user_password_hash = $this->uac_user->getPasswordHash();
        if(password_verify($this->current_password,$user_password_hash)){
            //echo "UpdatePasswordController update_password password verify\r\n";
            $user_id = $this->token->getUserId();
            $filter = ['_id' => new ObjectId($user_id)];
            $new_password_hash = password_hash($this->new_password,PASSWORD_DEFAULT);
            $values = ['$set' => [
                'password' => $new_password_hash
            ]];
            //echo "UpdatePasswordController update_password new password hash => ".var_export($new_password_hash,true)."\r\n";
            $password_update = $this->user->user_update($filter,$values);
            if($password_update) return true;
            else $this->errno = Upce::UPDATE_USER;
        }//if(password_verify($this->current_password,$this->uac_user->getPassword())){
        else $this->errno = Upce::CURRENT_PASSWORD_WRONG;
        return false;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        //echo "UpdatePasswordController setResponse errno => ".var_export($this->errno,true)."\r\n";
        switch($this->errno){
            case 0:
                $this->response = C::PASSWORD_UPDATE_OK;
                break;
            case Upce::CURRENT_PASSWORD_WRONG:
                $this->response = Upce::CURRENT_PASSWORD_WRONG_MSG;
                break;
            case Upce::FROM_USERAUTHORIZEDCONTROLLER:
                $errnoUac = $this->uac->getErrno();
                switch($errnoUac){
                    case Uace::FROM_TOKEN:
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response = C::PASSWORD_UPDATE_ERROR;
                                break;
                        }//switch($errnoT){
                        break;
                    case Uace::TOKEN_NOTFOUND:
                    case Uace::USER_NOTFOUND:
                        $this->response = C::PASSWORD_UPDATE_ERROR;
                        break;
                }//switch($errnoUac){
                break;
            case Upce::UPDATE_USER:
            default:
                $this->response = C::PASSWORD_UPDATE_ERROR;
                break;
        }
    }
}
?>