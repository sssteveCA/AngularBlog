<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\Email\EmailManager;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Subscribe\RegistrationControllerErrors as Rce;
use AngularBlog\Interfaces\UserErrors as Ue;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\RegistrationControllerTrait;
use AngularBlog\Traits\ResponseTrait;
use Exception;

//This class add subscriber data to DB and send activation email to user
class RegistrationController implements Rce,Ue,C{

    use ErrorTrait, ResponseTrait, RegistrationControllerTrait;

    private ?User $user; //User object with data to store in DB
    private ?string $headers = null; //Email activation headers
    private ?string $message = null; //Email activation body message

    public function __construct(?User $user)
    {
        if(!$user)throw new NoUserInstanceException(Rce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $reg = $this->registration();
        if($reg){
            //Send email if data are added to DB
            $this->sendEmail();
        }
        $this->setResponse();
    }

    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Rce::MAILNOTSENT:
                $this->error = Rce::MAILNOTSENT_MSG;
                break;
            case Rce::DUPLICATEVALUE:
                $this->error = Rce::DUPLICATEVALUE_MSG;
                break;
            case Rce::FROM_USER:
                $this->error = Rce::FROM_USER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    /**
     * Check if the provided email or username exist in database
     */
    private function checkDuplicate(): bool{
        $this->errno = 0;
        $user_cloned = clone $this->user;
        $exist = $user_cloned->user_get(['$or' => [
            'username' => $user_cloned->getUsername(), 'email' => $user_cloned->getEmail()
        ]]);
        if($exist){
            $this->errno = Rce::DUPLICATEVALUE;
            return true;
        }
        return false;
    }

    /**
     * store account registration values in DB
     */
    private function registration(): bool{
        $this->errno = 0;
        $registration = false;
        $create = $this->user->user_create();
        if($create){
            //Data added to DB
            $registration = true;
        }//if($create){
        else $this->errno = Rce::FROM_USER;
        return $registration;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = C::EMAIL_ACCOUNT_CREATED;
                break;
            case Rce::MAILNOTSENT:
                $this->response_code = 500;
                $this->response = C::EMAIL_ERROR;
                break;
            case Rce::FROM_USER:
                $errnoU = $this->user->getErrno();
                switch($errnoU){
                    case Ue::INVALIDDATAFORMAT:
                        $this->response_code = 400;
                        $this->response = Ue::INVALIDDATAFORMAT_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::REG_ERROR;
                        break;
                }
                break;
            default:
                $this->response_code = 500;
                $this->error = C::REG_ERROR;
                break;
        }
    }
}
?>