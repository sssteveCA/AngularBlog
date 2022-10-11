<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Comment\Comment;
use AngularBlog\Classes\Token;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Article\Comment\DeleteControllerErrors as Dce;
use AngularBlog\Classes\Article\Comment\CommentAuthorizedController;
use AngularBlog\Exceptions\CommentTypeMismatchException;
use AngularBlog\Exceptions\NoCommentInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Article\Comment\CommentAuthorizedControllerErrors as Cace;
use MongoDB\BSON\ObjectId;

class DeleteController implements Dce{
    use ErrorTrait, ResponseTrait;

    private ?Comment $comment;
    private ?Comment $cac_comment;
    private ?Token $token;
    private ?CommentAuthorizedController $cac;
    private static $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->comment = $data['comment'];
        $this->token = $data['token'];
        $auth = $this->checkAuthorization();
        if($auth){
            //User is authorized to delete this comment
            $del = $this->delete_comment();
        }
    }

    public function getComment(){return $this->comment;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Dce::FROM_COMMENTAUTHORIZEDCONTROLLER:
                $this->error = Dce::FROM_COMMENTAUTHORIZEDCONTROLLER_MSG;
                break;
            case Dce::COMMENTNOTDELETED:
                $this->error = Dce::COMMENTNOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
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
            $this->errno = Dce::FROM_COMMENTAUTHORIZEDCONTROLLER;
        return $authorized;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['comment']))throw new NoCommentInstanceException(Dce::NOCOMMENTINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Dce::NOTOKENINSTANCE_EXC);
        if(!$data['comment'] instanceof Comment)throw new CommentTypeMismatchException(Dce::INVALIDCOMMENTTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Dce::INVALIDTOKENTYPE_EXC);
    }

    //Delete the comment
    private function delete_comment(): bool{
        $del = false;
        $this->errno = 0;
        $comment_id = $this->comment->getId();
        $filter = ['_id' => new ObjectId($comment_id)];
        $comment_delete = $this->comment->comment_delete($filter);
        if($comment_delete)
            $del = true;
        else{
            $this->errno = Dce::COMMENTNOTDELETED;
        }
        return $del;
    }

    //Set the response to send to the view
    private function setResponse(){
        file_put_contents(DeleteController::$logFile,"DeleteController setResponse errno => {$this->errno}\r\n",FILE_APPEND);
        switch($this->errno){
            case 0:
                $this->response = "";
                break;
            case Dce::FROM_COMMENTAUTHORIZEDCONTROLLER:
                $cacErrno = $this->cac->getErrno();
                switch($cacErrno){
                    case Cace::FROM_TOKEN:
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response = C::COMMENTDELETE_ERROR;
                                break;
                        }
                        break;
                    case Cace::TOKEN_NOTFOUND:
                    case Cace::FORBIDDEN:
                        $this->response = Cace::FORBIDDEN_MSG;
                        break;
                    default:
                        $this->response = C::COMMENTDELETE_ERROR;
                        break;
                }//switch($Cacerrno){
                break;
            case Dce::COMMENTNOTDELETED:
            default:
                $this->response = C::COMMENTDELETE_ERROR;
                break;
        }
    }
}
?>