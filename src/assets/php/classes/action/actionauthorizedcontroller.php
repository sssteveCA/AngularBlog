<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ActionTypeMismatchException;
use AngularBlog\Exceptions\NoActionInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Action\ActionAuthorizedControllerErrors as Aace;
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
        }
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
}

?>