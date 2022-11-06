<?php

namespace AngularBlog\Classes\Logout;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Logout\LogoutControllerErrors as Loce;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class LogoutController implements C,Loce{

    use ErrorTrait, ResponseTrait;

    private ?Token $token;

    public function __construct(?Token $token)
    {
        if(!$token)throw new NoTokenInstanceException(Loce::NOTOKENINSTANCE_EXC);
        $this->token = $token;
        $this->logout();
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Loce::TOKENNOTDELETED:
                $this->error = Loce::TOKENNOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Remove the user logged collection from DB
    private function logout():bool{
        $logout = false;
        $this->errno = 0;
        $token_key = $this->token->getTokenKey();
        $filter = ['token_key' => $token_key];
        $del = $this->token->token_delete($filter);
        if($del){
            //Token successfully deleted
            $logout = true;
        }//if($del){
        else
            $this->errno = Loce::TOKENNOTDELETED;
        return $logout;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = ""; //No response, redirect to home page
                break;
            case Loce::TOKENNOTDELETED:
            default:
                $this->response_code = 500;
                $this->response = C::LOGOUT_ERROR;
                break;
        }
    }

}
?>