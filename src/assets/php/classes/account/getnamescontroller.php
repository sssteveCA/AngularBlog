<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;
use AngularBlog\Interfaces\Account\GetNamesControllerErrors as Gnce;
use AngularBlog\Interfaces\TokenErrors as Te;
use MongoDB\BSON\ObjectId;

/**
 * Used to get name and the surname of the logged user
 */
class GetNamesController{

    use ErrorTrait, ResponseMultipleTrait; 

    private ?Token $token;
    private ?User $user;
    private string $name;
    private string $surname;

    public function __construct(array $data){
        $this->checkValues($data);
        $this->getNames();
        $this->setResponse();
    }

    public function getToken(){ return $this->token; }
    public function getName(){ return $this->name; }
    public function getSurname(){ return $this->surname; }
    public function getError(){
        switch($this->errno){
            case Gnce::FROM_TOKEN:
                $this->error = Gnce::FROM_TOKEN_MSG;
                break;
            case Gnce::FROM_USER:
                $this->error = Gnce::FROM_USER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
    }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Gnce::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Gnce::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Gnce::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Gnce::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    private function getNames(): bool{
        $this->errno = 0;
        $token_key = $this->token->getTokenKey();
        $this->token->token_get(['token_key' => $token_key]);
        if($this->token->getErrno() != 0){
            $this->errno = Gnce::FROM_TOKEN;
            return false;
        } 
        $this->token->expireControl();
        if($this->token->getErrno() != 0){
            $this->errno = Gnce::FROM_TOKEN;
            return false;
        }
        $user_id = $this->token->getUserId();
        $this->user->user_get(['_id' => new ObjectId($user_id)]);
        if($this->user->getErrno() != 0){
            $this->errno = Gnce::FROM_USER;
            return false;
        }
        $this->name = $this->user->getName();
        $this->surname = $this->user->getSurname();
        return true;
    }

    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_array = ['name' => $this->name, 'surname' => $this->surname];
                $this->response_code = 200;
                break;
            case Gnce::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response_code = 401;
                        $this->response = "EXPIRED";
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = "";
                        break;
                }
                break;
            case Gnce::FROM_USER:
                $this->response_code = 500;
                $this->response = "";
                break;
            default:
                $this->response_code = 500;
                $this->response = "";
                break;
        }
    }
}

?>