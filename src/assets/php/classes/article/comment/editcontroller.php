<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Classes\Action\Action;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Comment\CommentAuthorizedController;
use AngularBlog\Classes\Myarticles\EditContoller;
use AngularBlog\Exceptions\CommentTypeMismatchException;
use AngularBlog\Exceptions\NoCommentInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use MongoDB\BSON\ObjectId;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedControllerErrors as Cace;

class EditController implements Ece{
    use ErrorTrait, ResponseTrait;

    private ?Comment $comment;
    private ?Comment $cac_comment; //Comment used by CommentAuthorizationController class
    private ?CommentAuthorizedController $cac;
    private ?Token $token;
    private ?Action $action;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->comment = $data['comment'];
        $this->token = $data['token'];
        if($this->checkAuthorization()){
            if($this->edit_comment())
                $this->addAction();
        }
        $this->setResponse();

    }

    public function getComment(){return $this->comment;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Ece::FROM_COMMENTAUTHORIZEDCONTROLLER:
                $this->error = Ece::FROM_COMMENTAUTHORIZEDCONTROLLER_MSG;
                break;
            case Ece::FROM_ACTION:
                $this->error = Ece::FROM_ACTION_MSG;
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

    /**
     * Check if array provided has valid values
     **/
    private function checkValues(array $data){
        if(!isset($data['comment']))throw new NoCommentInstanceException(Ece::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new CommentTypeMismatchException(Ece::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Ece::INVALIDTOKENTYPE_EXC);
    }

     /**
     * Add an action to rememeber the done operation
     */
    private function addAction(): bool{
        $this->action = new Action([
            'user_id' => $this->token->getUserId(),
            'title' => 'Modifica commento',
            'description' => <<<HTML
Hai modificato il commento "{$this->cac_comment->getComment()}" in "{$this->comment->getComment()}"
HTML
        ]);
        $insert = $this->action->action_create();
        if(!$insert) $this->errno = Ece::FROM_ACTION;
        return true;
    }

    /**
     * Check if user is authorized to edit the comment
     **/
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

    /**
     * Update comment information
     **/
    private function edit_comment(): bool{
        $edited = false;
        $this->errno = 0;
        $comment_id = $this->comment->getId();
        $filter = ['_id' => new ObjectId($comment_id)];
        $values = ['$set' => [
            'comment' => $this->comment->getComment()
        ]];
        $comment_edit = $this->comment->comment_update($filter,$values);
        if($comment_edit)
            $edited = true;
        else
            $this->errno = Ece::COMMENTNOTUPDATED;
        return $edited;
    }

    /**
     * Set the response to send to the view
     **/
    private function setResponse(){
        switch($this->errno){
            case 0:
            case Ece::FROM_ACTION:
                $this->response_code = 200;
                $this->response = "OK";
                break;
            case Ece::FROM_COMMENTAUTHORIZEDCONTROLLER:
                $aacErrno = $this->aac->getErrno();
                switch($aacErrno){
                    case Cace::FROM_TOKEN:
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response_code = 401;
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response_code = 500;
                                $this->response = C::COMMENTUPDATE_ERROR;
                                break;
                        }
                        break;
                    case Cace::TOKEN_NOTFOUND:
                    case Cace::FORBIDDEN:
                        $this->response_code = 403;
                        $this->response = Cace::FORBIDDEN_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::COMMENTUPDATE_ERROR;
                        break;
                }//switch($aacErrno){
                break;
            case Ece::COMMENTNOTUPDATED:
            default:
                $this->response_code = 500;
                $this->response = C::COMMENTUPDATE_ERROR;
                break;
        }
    }
}

?>