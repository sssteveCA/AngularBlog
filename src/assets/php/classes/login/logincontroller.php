<?php

namespace AngularBlog\Classes\Login;

use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Login\LoginControllerErrors as Lce;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoUserInstanceException;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\BulkWriteException;
use AngularBlog\Traits\ErrorTrait;

class LoginController implements Lce,C{

    use ErrorTrait;

    private ?User $user; //User object with data to store in DB
    private ?Token $token; //Returned token when login has success
    private string $response = ""; //Response message

    public function __construct(?User $user)
    {
        if(!$user)throw new NoUserInstanceException(Lce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $login = $this->login();
        if($login){
            $token_set = $this->setToken();
        }
		$this->setResponse();
    }

    public function getUser(){return $this->user;}
    public function getToken(){return $this->token;}
    public function getResponse(){return $this->response;}
    public function getError(){
        switch($this->errno){
            case Lce::USERNAMENOTFOUND:
                $this->error = Lce::USERNAMENOTFOUND_MSG;
                break;
            case Lce::WRONGPASSWORD:
                $this->error = Lce::WRONGPASSWORD_MSG;
                break;
            case Lce::ACCOUNTNOTACTIVATED:
                $this->error = Lce::ACCOUNTNOTACTIVATED_MSG;
                break;
            case Lce::TOKENNOTSETTED:
                $this->error = Lce::TOKENNOTSETTED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function login(): bool{
        $logged = false;
        $this->errno = 0;
        $filter = ['username' => $this->user->getUsername()];
        //Check if user with username given exists
        $found = $this->user->user_get($filter);
        if($found){
            //Check if password is correct
            $password = $this->user->getPassword();
            $passwordHash = $this->user->getPasswordHash();
            if(password_verify($password,$passwordHash)){
                //Check if user account is email validated
                if($this->user->isSubscribed()){
                    $logged = true;
                }//if($this->user->isSubscribed()){
                else 
                    $this->errno = Lce::ACCOUNTNOTACTIVATED;
            }//if(password_verify($password,$passwordHash)){
            else
                $this->errno = Lce::WRONGPASSWORD;
        }//if($found){
        else
            $this->errno = Lce::USERNAMENOTFOUND;
        return $logged;
    }

    private function setToken(): bool{
        $set = false;
        $this->errno = 0;
        $logged_time = date('d-m-Y H:i:s');
        $token_old = new Token();
        $user_id = $this->user->getId();
        $data_old = ['user_id' => new ObjectId($user_id)];
        //Check if a token with this user_id exists
        $get_old = $token_old->token_get($data_old);
        if($get_old){
            //Found an old user session rewrite
            $this->token = new Token(['username' => $token_old->getUsername()]);
            $filter = $data_old;
            $values = [
                '$set' => ['username' => $this->user->getUsername()] 
            ];
            $token_update = $this->token->token_update($filter,$values);
            if($token_update){
                $set = true;
            }
            else Lce::TOKENNOTSETTED;
        }//if($get_old){
        else{
            //No previous user session found
            $data = [
                'user_id' => $this->user->getId(),
                'username' => $this->user->getUsername(),
                'logged_time' => $logged_time
            ];
            $this->token = new Token($data);
            $insert = $this->token->token_create();
            if($insert)$set = true;
            else
                $this->errno = Lce::TOKENNOTSETTED; 
        }//else if($get_old){
        return $set;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = ""; //No response, redirect to personal area
                break;
            case Lce::USERNAMENOTFOUND:
            case Lce::WRONGPASSWORD:
            case Lce::ACCOUNTNOTACTIVATED:
                $this->response = $this->getError();
                break;
            case Lce::TOKENNOTSETTED:
                $this->response = C::LOGIN_ERROR;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
                break;

        }
    }
}
?>