<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ActionTypeMismatchException;
use AngularBlog\Exceptions\NoActionInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Action\ActionAuthorizedControllerErrors as Aace;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use MongoDB\BSON\ObjectId;

class ActionAuthorizedController implements Aace{

    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Action $action;
    private ?Token $token;

    private function __construct(array $data){
        $this->checkValues($data);
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            $actionOk = $this->getActionById();
            if($actionOk){
                $this->isUserAuthorizedCheck();
            }
        }
        $this->setResponse();
    }

    public function getAction(){return $this->action;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Aace::ACTION_NOTFOUND:
                $this->error = Aace::ACTION_NOTFOUND_MSG;
                break;
            case Aace::TOKEN_NOTFOUND:
                $this->error = Aace::TOKEN_NOTFOUND_MSG;
                break;
            case Aace::FORBIDDEN:
                $this->error = Aace::FORBIDDEN_MSG;
                break;
            case Aace::FROM_TOKEN:
                $this->error = Aace::FROM_TOKEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    /**
     * Check if values inside array are Action and Token types
     */
    private function checkValues(array $data){
        if(!isset($data['action'])) throw new NoActionInstanceException(Aace::NOACTIONINSTANCE_EXC);
        if(!isset($data['token'])) throw new NoTokenInstanceException(Aace::NOTOKENINSTANCE_EXC);
        if(!$data['action'] instanceof Action)throw new ActionTypeMismatchException(Aace::ARTICLETYPEMISMATCH_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Aace::TOKENTYPEMISMATCH_EXC);
    }

    /**
     * Get action info by id
     */
    private function getActionById(): bool{
        $action_id = $this->action->getId();
        $data = ['_id' => new ObjectId($action_id)];
        $action_got = $this->action->action_get($data);
        if($action_got)
            return true;
        $this->errno = Aace::ACTION_NOTFOUND;
        return false;
    }

    /**
     * Get token by token key
     */
    private function getTokenByKey(): bool{
        $this->errno = 0;
        $data = ['token_key' => $this->token->getTokenKey()];
        $token_got = $this->token->token_get($data);
        if($token_got){
            $this->token->expireControl();
            if(!$this->token->isExpired())
                return true;
            $this->errno = Aace::FROM_TOKEN;
            return false;    
        }
        $this->errno = Aace::TOKEN_NOTFOUND;
        return false;
    }

    /**
     * Check if user is authorized to manage this action
     */
    private function isUserAuthorizedCheck(): bool{
        $this->authorized = false;
        $this->errno = 0;
        $token_user_id = $this->token->getUserId();
        $action_user_id = $this->action->getUserId();
        if($token_user_id == $action_user_id){
            $this->authorized = true;
            return true;
        }
        $this->errno = Aace::FORBIDDEN;
        return false;
    }

    /**
     * Set the response data
     */
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = "OK";
                break;
            case Aace::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response_code = 401;
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::ERROR_UNKNOWN;
                        break;
                }//switch($errnoT){
                break;
            case Aace::TOKEN_NOTFOUND:
            case Aace::FORBIDDEN:
                $this->response_code = 403;
                $this->response = Aace::FORBIDDEN_MSG;
                break;
            case Aace::ACTION_NOTFOUND:
                $this->response_code = 404;
                $this->response = Aace::ACTION_NOTFOUND_MSG;
                break;
            default:
                $this->response_code = 500;
                $this->response = C::ERROR_UNKNOWN;
                break;
        }
    }


}

?>