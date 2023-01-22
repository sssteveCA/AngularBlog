<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Interfaces\Account\GetUserInfoControllerErrors as Guice;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseMultipleTrait;
use MongoDB\BSON\ObjectId;

/**
 * Used to get the information about the logged user
 */
class GetUserInfoController implements Guice{

    use ErrorTrait, ResponseMultipleTrait;

    private ?Token $token;
    private ?User $user;
    private string $email;
    private string $name;
    private string $surname;
    private string $username;

    public function __construct(array $data){

    }
    
    public function getToken(){ return $this->token; }
    public function getEmail(){ return $this->email; }
    public function getName(){ return $this->name; }
    public function getSurname(){ return $this->surname; }
    public function getUsername(){ return $this->username; }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new NoTokenInstanceException(Guice::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Guice::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Guice::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Guice::USERTYPEMISMATCH_EXC);
        $this->token = $data['token'];
        $this->user = $data['user'];
    }

    /**
     * Get the info about the logged user providing the token key
     * @return bool true if the information was obtained, false otherwise
     */
    private function getUserInfo(): bool{
        $this->errno = 0;
        $token_key = $this->token->getTokenKey();
        $this->token->token_get(['token_key' => $token_key]);
        if($this->token->getErrno() != 0){
            $this->errno = Guice::FROM_TOKEN;
            return false;
        }
        $this->token->expireControl();
        if($this->token->getErrno() != 0){
            $this->errno = Guice::FROM_TOKEN;
            return false;
        }
        $user_id = $this->token->getUserId();
        $this->user->user_get(['_id' => new ObjectId($user_id)]);
        if($this->user->getErrno() != 0){
            $this->errno = Guice::FROM_USER;
            return false;
        }
        $this->email = $this->user->getEmail();
        $this->name = $this->user->getName();
        $this->surname = $this->user->getSurname();
        $this->username = $this->user->getUsername();
        return true;
    }

}
?>