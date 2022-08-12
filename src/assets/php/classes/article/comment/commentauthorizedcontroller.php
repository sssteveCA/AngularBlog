<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedControllerErrors as Cace;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Constants as C;

//Determine if user is authorized to do operations with specificf comment
class CommentAuthorizedController implements Cace{
    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Comment $comment;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkVariables($data);
        $this->comment = $data['comment'];
        $this->token = $data['token'];

    }

    public function getComment(){return $this->comment;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Cace::COMMENT_NOTFOUND:
                $this->error = Cace::COMMENT_NOTFOUND_MSG;
                break;
            case Cace::TOKEN_NOTFOUND:
                $this->error = Cace::TOKEN_NOTFOUND_MSG;
                break;
            case Cace::FORBIDDEN:
                $this->error = Cace::FORBIDDEN_MSG;
                break;
            case Cace::FROM_TOKEN:
                $this->error = Cace::FROM_TOKEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if values inside array are Article,User,Token types
    private function checkVariables(array $data){
        if(!isset($data['comment']))throw new \Exception(Cace::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Cace::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new \Exception(Cace::COMMENTTYPEMISMATCH_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Cace::TOKENTYPEMISMATCH_EXC);
    }

    //Get token by token key
    private function getTokenByKey(): bool{
        $got = false;
        $this->errno = 0;
        $key = $this->token->getTokenKey();
        $data = ['token_key' => $key];
        $token_got = $this->token->token_get($data);
        if($token_got){
            //Check if token is expired
            $this->token->expireControl();
            if($this->token->isExpired()){
                $this->errno = Cace::FROM_TOKEN;
            }
            else
                $got = true;
        }
        else
            $this->errno = Cace::TOKEN_NOTFOUND;
        return $got;
    }
}
?>