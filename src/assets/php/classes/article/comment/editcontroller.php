<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Comment\CommentAuthorizedController;

class EditController implements Ece{
    use ErrorTrait, ResponseTrait;

    private ?Comment $article;
    private ?Comment $cac_comment; //Comment used by CommentAuthorizationController class
    private ?CommentAuthorizedController $aac;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->comment = $data['comment'];
        $this->token = $data['token'];
    }

    public function getComment(){return $this->comment;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Ece::FROM_COMMENTAUTHORIZEDCONTROLLER:
                $this->error = Ece::FROM_COMMENTAUTHORIZEDCONTROLLER_MSG;
                break;
            case Ece::COMMENTNOTUPDATED:
                $this->error = Ece::COMMENTNOTUPDATED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['comment']))throw new \Exception(Ece::NOACOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new \Exception(Ece::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Ece::INVALIDTOKENTYPE_EXC);
    }

    //Check if user is authorized to edit the article
    private function checkAuthorization(): bool{
        $authorized = false;
        $this->errno = 0;
        $this->cac_comment = clone $this->comment;
        $this->cac = new CommentAuthorizedController([
            'comment' => $this->cac_comment,
            'token' => $this->token
        ]);
        $cacErrno = $this->cac->getErrno();
        if($cacErrno == 0){
            $authorized = true;
        }
        else
            $this->errno = Ece::FROM_COMMENTAUTHORIZEDCONTROLLER;
        return $authorized;
    }
}

?>