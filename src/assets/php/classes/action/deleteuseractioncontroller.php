<?php


use AngularBlog\Classes\Action\Action;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\Action\DeleteUserActionControllerErrors as Duace;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class DeleteUserActionController implements Duace{

    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?Action $action;
    private ?Action $aac_action;

    public function __construct(array $data){

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
        if(!isset($data['action'])){
            
        }
    }

}

?>