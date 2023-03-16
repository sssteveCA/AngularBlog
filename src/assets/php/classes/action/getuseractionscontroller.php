<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Classes\ActionList;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;
use AngularBlog\Interfaces\Action\GetUserActionControllerErrors as Guace;
use MongoDB\BSON\ObjectId;

class GetUserActionsController implements Guace{

    use ErrorTrait, ResponseMultipleTrait;

    private ?string $token_key;
    private ?ActionList $actionList;
    private ?Token $token;

    public function __construct(array $data)
    {
        if(!isset($data['token_key']))throw new \Exception(Guace::NOTOKENKEY_EXC);
        $this->token_key = $data['token_key'];
        $this->actionList = new ActionList();
        if($this->setToken()){

        }
    }

    public function getTokenKey(){ return $this->token_key; }
    public function getActionList(){ return $this->actionList; }
    public function getError(){
        switch($this->errno){
            case Guace::NOUSERIDFOUND:
                $this->error = Guace::NOUSERIDFOUND_MSG;
                break;
            case Guace::NOACTIONFOUND:
                $this->error = Guace::NOACTIONFOUND_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    /**
     * Set the Token object
     */
    private function setToken(): bool{
        $this->token_key = new Token();
        $filter = ['token_key' => $this->token_key];
        $get = $this->token->token_get($filter);
        if($get) return true;
        $this->errno = Guace::NOUSERIDFOUND;
        return false;
    }

    /**
     * Get the actions performed by specific user
     */
    private function setUserActions(): bool{
        $user_id = $this->token->getUserId();
        $filter = ['user_id' => new ObjectId($user_id)];
        $actionsGet = $this->actionList->actionlist_get($filter);
        if($actionsGet) return true;
        $this->errno = Guace::NOACTIONFOUND;
        return false;
    }
}
?>