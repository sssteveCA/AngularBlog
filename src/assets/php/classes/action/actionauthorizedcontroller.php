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

class ActionAuthorizedController implements Aace{

    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Action $action;
    private ?Token $token;

    private function __construct(array $data){
        $this->checkValues($data);
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
}

?>