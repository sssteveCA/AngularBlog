<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\CommentTypeMismatchException;
use AngularBlog\Exceptions\NoCommentInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedControllerErrors as Cace;
use AngularBlog\Traits\AuthorizedTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Constants as C;
use MongoDB\BSON\ObjectId;
use AngularBlog\Interfaces\TokenErrors as Te;

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
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            //Token exists
            $commentOk = $this->getCommentById();
            if($commentOk){
                //Comment Exists
                $authOk = $this->isUserAuthorizedCheck();
            }
        }//if($tokenOk){
        $this->setResponse();
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
        if(!isset($data['comment']))throw new NoCommentInstanceException(Cace::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Cace::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new CommentTypeMismatchException(Cace::COMMENTTYPEMISMATCH_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Cace::TOKENTYPEMISMATCH_EXC);
    }

    //Get article info by id
    private function getCommentById(): bool{
        $got = false;
        $this->errno = 0;
        $comment_id = $this->comment->getId();
        $data = ['_id' => new ObjectId($comment_id)];
        $comment_got = $this->comment->comment_get($data);
        if($comment_got){
            $got = true;
        }
        else
            $this->errno = Cace::COMMENT_NOTFOUND;
        return $got;
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

    //Check if user is authorized to edit this comment
    private function isUserAuthorizedCheck(): bool{
        $this->authorized = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $comment_author = $this->comment->getAuthor();
        if($user_id == $comment_author){
            //User is the owner of the article
            $this->authorized = true;
        }
        else
            $this->errno = Cace::FORBIDDEN;
        return $this->authorized;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = "OK";
                break;
            case Cace::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response = C::ERROR_UNKNOWN;
                        break;
                }//switch($errnoT){
                break;
            case Cace::TOKEN_NOTFOUND:
            case Cace::FORBIDDEN:
                $this->response = Cace::FORBIDDEN_MSG;
                break;
            case Cace::COMMENT_NOTFOUND:
                $this->response = Cace::COMMENT_NOTFOUND_MSG;
                break;
            default:
                $this->response = C::ERROR_UNKNOWN;
                break;
        }//switch($this->errno){
    }
}
?>