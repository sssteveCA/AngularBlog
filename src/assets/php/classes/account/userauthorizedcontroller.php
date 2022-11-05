<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Account\UserAuthorizedControllerErrors as Uace;
use MongoDB\BSON\ObjectId;

/**
 * Check if user is authorized to manage the account
 */
class UserAuthorizedController implements Uace{
    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Token $token;
    private ?User $user;

    public function __construct(array $data)
    {
        $this->checkVariables($data);
        if($this->getTokenByKey()){
            if($this->getUserByTokenKey())
                $this->authorized = true;
        }//if($this->getTokenByKey()){
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Uace::TOKEN_NOTFOUND:
                $this->error = Uace::TOKEN_NOTFOUND_MSG;
                break;
            case Uace::USER_NOTFOUND:
                $this->error = Uace::USER_NOTFOUND_MSG;
                break;
            case Uace::FROM_TOKEN:
                $this->error = Uace::FROM_TOKEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    /**
     * Check if required data exists and are in a valid format
     */
    private function checkVariables(array $data){
        if(!isset($data['token']))throw new NoTokenInstanceException(Uace::NOTOKENINSTANCE_EXC);
        if(!isset($data['user']))throw new NoUserInstanceException(Uace::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Uace::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User)throw new UserTypeMismatchException(Uace::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    /**
     * Check if token exist and it isn't expired
     */
    private function getTokenByKey(): bool{
        echo "UserAuthorizedControllergetTokenByKey\r\n";
        $this->errno = 0;
        $key = $this->token->getTokenKey();
        $data = ['token_key' => $key];
        $token_got = $this->token->token_get($data);
        if($token_got){
            $this->token->expireControl();
            if(!$this->token->isExpired())
                return true;
            else
                $this->errno = Uace::FROM_TOKEN;
        }
        else
            $this->errno = Uace::TOKEN_NOTFOUND;
        return false;
    }


    /**
     * Get the user info that has the checked token key
     */
    private function getUserByTokenKey(): bool{
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        echo "UserAutorizedController user_id =>".var_export($user_id,true)."\r\n";
        $filter = ['_id' => new ObjectId($user_id)];
        $got = $this->user->user_get($filter);
        if($got) return true;
        else $this->errno = Uace::USER_NOTFOUND;
        return false;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        echo "UpdateAuthorizedController setResponse errno =>".var_export($this->errno,true)."\r\n";
        switch($this->errno){
            case 0:
                $this->response = "OK";
                break;
            case Uace::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response = C::ERROR_UNKNOWN;
                        break;
                }//switch($errnoT){
                break;
            case Uace::TOKEN_NOTFOUND:
                $this->response = Uace::TOKEN_NOTFOUND_MSG;
                break;
            case Uace::USER_NOTFOUND:
                $this->response = Uace::USER_NOTFOUND_MSG;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
                break;
        }//switch($this->errno){
    }
}
?>