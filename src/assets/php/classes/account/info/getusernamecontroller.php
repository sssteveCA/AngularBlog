<?php

namespace AngularBlog\Classes\Account\Info;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Account\Info\GetUsernameControllerErrors as Guce;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Constants as C;

class GetUsernameController implements Guce {
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?User $user;
    private string $username;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->token = $data['token'];
        $this->user = $data['user'];
        $this->getUsername();
        $this->setResponse();
    }

    public function getError(){
        switch($this->errno){
            case Guce::FROM_TOKEN:
                $this->error = Guce::FROM_TOKEN_MSG;
                break;
            case Guce::FROM_USER:
                $this->error = Guce::FROM_USER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
    }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Guce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Guce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Guce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Guce::USERTYPEMISMATCH_EXC);
    }

    private function getUsername(): bool{
        $this->errno = 0;
        $token_key = $this->token->getTokenKey();
        $token_get = $this->token->token_get(['token_key' => $token_key]);
        if($this->token->getErrno() != 0){
            $this->errno = Guce::FROM_TOKEN;
            return false;
        } 
        $user_id = $this->token->getUserId();
        $user_get = $this->user->user_get(['_id' => $user_id]);
        if($this->user->getErrno() != 0){
            $this->errno = Guce::FROM_USER;
            return false;
        }
        $this->username = $this->user->getUsername();
        return true;
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = $this->username;
                break;
            case Guce::FROM_TOKEN:
                $this->response = "";
                break;
            case Guce::FROM_USER:
                $this->response = "";
                break;
            default:
                $this->response = "";
                break;
        }
    }
}
?>