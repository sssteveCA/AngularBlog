<?php

namespace AngularBlog\Classes\Logout;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Logout\LogoutControllerErrors as Loce;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Traits\ErrorTrait;

class LogoutController implements C,Loce{

    use ErrorTrait;

    private ?Token $token;
    private ?string $response = "";

    public function __construct(?Token $token)
    {
        if(!$token)throw new NoTokenInstanceException(Loce::NOTOKENINSTANCE_EXC);
        $this->token = $token;
        $this->logout();
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getResponse(){return $this->response;}
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
                $this->response = ""; //No response, redirect to home page
                break;
            case Loce::TOKENNOTDELETED:
                $this->response = C::LOGOUT_ERROR;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
                break;
        }
    }

}
?>