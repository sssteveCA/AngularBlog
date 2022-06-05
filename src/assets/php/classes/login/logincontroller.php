<?php

namespace AngularBlog\Classes\Login;

use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Login\LoginControllerErrors as Lce;
use AngularBlog\Classes\User;

class LoginController implements Lce,C{
    private ?User $user; //User object with data to store in DB
    private ?Token $token; //Returned token when login has success
    private string $response = ""; //Response message
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?User $user)
    {
        if(!$user)throw new \Exception(Lce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $this->login();
        $this->setResponse();
        $this->setToken();
    }

    public function getUser(){return $this->user;}
    public function getToken(){return $this->token;}
    public function getResponse(){return $this->response;}
    public function getErrno(): int{return $this->errno;}
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
        $data = [
            'user_id' => $this->user->getId(),
            'username' => $this->user->getUsername(),
            'logged_time' => $logged_time
        ];
        $data_get = ['user_id' => $this->user->getId()];
        $this->token = new Token($data);
        $get = $this->token->token_get($data_get);
        if($get)$set = true;
        if(!$get){
            $insert = $this->token->token_create();
            if($insert)$set = true;
            else
                $this->errno = Lce::TOKENNOTSETTED;   
        }
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
                $this->response = $this->error;
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