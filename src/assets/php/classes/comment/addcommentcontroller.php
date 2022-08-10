<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\AddCommentControllerErrors as Acce;

class AddCommentController implements Acce{
    use ErrorTrait, ResponseTrait;

    private ?string $permalink;
    private ?string $token_key;
    private ?string $comment_text;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->permalink = $data['permalink'];
        $this->comment_text = $data['comment_text'];
        $this->token_key = $data['token_key'];   
    }

    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['permalink']))throw new \Exception(Acce::NOARTICLEPERMALINK_EXC);
        if(!isset($data['comment']))throw new \Exception(Acce::NOCOMMENT_EXC);
        if(!isset($data['token_key']))throw new \Exception(Acce::NOTOKENKEY_EXC);
    }

    //Set the Token object
    private function setToken(): bool{
        $set = false;
        $this->errno = 0;
        $this->token = new Token();
        $filter = ['token_key' => $this->token_key];
        $get = $this->token->token_get($filter);
        if($get){
            //Check if token is expired
            $this->token->expireControl();
            if($this->token->isExpired()){
                $this->errno = Acce::FROM_TOKEN;
            }
            else
                $set = true;
        }
        else
            $this->errno = Acce::NOUSERIDFOUND;
        //file_put_contents(CreateController::$logFile,"setToken() result => ".var_export($set,true)."\r\n",FILE_APPEND);
        return $set;
    }

}
?>