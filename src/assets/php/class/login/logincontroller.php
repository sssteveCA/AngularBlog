<?php

namespace AngularBlog\Classes\Login;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Login\LoginControllerErrors as Lce;
use AngularBlog\Classes\User;

class LoginController implements Lce,C{
    private ?User $user; //User object with data to store in DB
    private string $response = ""; //Response message
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?User $user)
    {
        if(!$user)throw new \Exception(Lce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $this->login();
        $this->setResponse();
    }

    public function getUser(){return $this->user;}
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
            default:
                $this->response = C::ERROR_UNKNOWN;
                break;

        }
    }
}
?>