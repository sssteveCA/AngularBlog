<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Account\UpdateUsernameControllerErrors as Uuce;
use AngularBlog\Interfaces\Account\UserAuthorizedControllerErrors as Uace;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use MongoDB\BSON\ObjectId;


class UpdateUsernameController implements Uuce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $auth = $this->checkAuthorization();
        if($auth){
            $this->update_username();
        }
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Uuce::UPDATE_TOKEN:
                $this->errno = Uuce::UPDATE_TOKEN_MSG;
                break;
            case Uuce::UPDATE_USER:
                $this->error = Uuce::UPDATE_USER_MSG;
                break;
            case Uuce::FROM_USERAUTHORIZEDCONTROLLER:
                $this->error = Uuce::FROM_USERAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function checkValues(array $data){
        if(!isset($data['token']))throw new NoTokenInstanceException(Uuce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user']))throw new NoUserInstanceException(Uuce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Uuce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User)throw new UserTypeMismatchException(Uuce::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    /**
     * Check if user is authorized to update the username
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        $this->uac = new UserAuthorizedController([
            'token' => $this->token,
            'user' => $this->uac_user
        ]);
        $uacErrno = $this->uac->getErrno();
        if($uacErrno == 0) return true;
        $this->errno = Uuce::FROM_USERAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Update the username of the logged account
     */
    private function update_username(): bool{
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $new_username = $this->user->getUsername();
        $filter = ['_id' => new ObjectId($user_id)];
        $values = ['$set' => [
            'username' => $new_username
        ]];
        $user_update = $this->user->user_update($filter,$values);
        if($user_update){
            $filter = [
                'token_key' => $this->token->getTokenKey()
            ];
            $values = [ '$set' => [
                'username' => $new_username
            ]];
            $token_update = $this->token->token_update($filter,$values);
            if($token_update) return true;
            else $this->errno = Uuce::UPDATE_TOKEN;
        }//if($user_update){
        else $this->errno = Uuce::UPDATE_USER;
        return false;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = C::USERNAME_UPDATE_OK;
                break;
            case Uuce::FROM_USERAUTHORIZEDCONTROLLER:
                $errnoUac = $this->uac->getErrno();
                switch($errnoUac){
                    case Uace::FROM_TOKEN:
                        $errnoT = $this->uac->getToken()->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response_code = 401;
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response_code = 500;
                                $this->response = C::USERNAME_UPDATE_ERROR;
                                break;
                        }//switch($errnoT){
                        break;
                    case Uace::TOKEN_NOTFOUND:
                    case Uace::USER_NOTFOUND:
                        $this->response_code = 500;
                        $this->response = C::PASSWORD_UPDATE_ERROR;
                        break;
                }//switch($errnoUac){
                break;
            case Uuce::UPDATE_USER:
            case Uuce::UPDATE_TOKEN:
            default:
                $this->response_code = 500;
                $this->response = C::USERNAME_UPDATE_ERROR;
                break;
        }//switch($this->errno){
    }

}
?>