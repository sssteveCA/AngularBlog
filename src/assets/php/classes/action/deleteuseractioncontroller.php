<?php


use AngularBlog\Classes\Action\Action;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ActionTypeMismatchException;
use AngularBlog\Exceptions\NoActionInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Action\DeleteUserActionControllerErrors as Duace;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class DeleteUserActionController implements Duace{

    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?Action $action;
    private ?Action $aac_action;

    public function __construct(array $data){
        $this->checkValues($data);
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
                break;
        }
    }

    /**
     * Check if array provided has valid values
     */
    private function checkValues(array $data){
        if(!isset($data['action']))throw new NoActionInstanceException(Duace::NOACTIONINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Duace::NOTOKENINSTANCE_EXC);
        if(!$data['action'] instanceof Action) throw new ActionTypeMismatchException(Duace::INVALIDACTIONTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Duace::INVALIDTOKENTYPE_EXC);
    }

}

?>