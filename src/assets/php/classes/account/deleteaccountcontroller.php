<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Classes\Action\Action;
use AngularBlog\Classes\ActionList;
use AngularBlog\Classes\Article\ArticleList;
use AngularBlog\Classes\Comment\CommentList;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\User;
use AngularBlog\Exceptions\MissingValuesException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\NoUserInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Exceptions\UserTypeMismatchException;
use AngularBlog\Interfaces\Account\DeleteAccountControllerErrors as Dace;
use AngularBlog\Interfaces\Account\UserAuthorizedControllerErrors as Uace;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use MongoDB\BSON\ObjectId;

class DeleteAccountController implements Dace{
    use ErrorTrait, ResponseTrait;

    private string $conf_password;
    private string $password;

    private ?ActionList $action_list;
    private ?ArticleList $article_list;
    private ?CommentList $comment_list;
    private ?Token $token;
    private ?User $user;
    private ?User $uac_user;
    private ?UserAuthorizedController $uac;

    public function __construct(array $data){
        $this->checkValues($data);
        $auth = $this->checkAuthorization();
        if($auth)
            $this->delete_account();
        $this->setResponse();
    }
   
    public function getActionList(){return $this->action_list;}
    public function getArticlesList(){return $this->article_list;}
    public function getCommentsList(){return $this->comment_list;}
    public function getToken(){return $this->token;}
    public function getUser(){return $this->user;}
    public function getError(){
        switch($this->errno){
            case Dace::CURRENT_PASSWORD_WRONG:
                $this->error = Dace::CURRENT_PASSWORD_WRONG_MSG;
                break;
            case Dace::DELETE_USER:
                $this->error = Dace::DELETE_USER_MSG;
                break;
            case Dace::FROM_USERAUTHORIZEDCONTROLLER:
                $this->error = Dace::FROM_USERAUTHORIZEDCONTROLLER_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    private function checkValues(array $data){
        if(!isset($data['conf_password'],$data['password']))throw new MissingValuesException(Dace::MISSINGVALUES_EXC);
        if(!isset($data['token'])) throw new NoTokenInstanceException(Dace::NOTOKENINSTANCE_EXC);
        if(!isset($data['user'])) throw new NoUserInstanceException(Dace::NOUSERINSTANCE_EXC);
        if(!$data['token'] instanceof Token) throw new TokenTypeMismatchException(Dace::TOKENTYPEMISMATCH_EXC);
        if(!$data['user'] instanceof User) throw new UserTypeMismatchException(Dace::USERTYPEMISMATCH_EXC);
        $this->conf_password = $data['conf_password'];
        $this->password = $data['password'];
        $this->token = $data['token'];
        $this->user = $data['user'];
        $this->action_list = new ActionList();
        $this->article_list = new ArticleList();
        $this->comment_list = new CommentList();
    }

    /**
     * Check if the user is authorized to delete the account
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->uac_user = clone $this->user;
        $this->uac = new UserAuthorizedController([
            'token' => $this->token, 'user' => $this->uac_user
        ]);
        $uacErrno = $this->uac->getErrno();
        if($uacErrno == 0) return true;
        $this->errno = Dace::FROM_USERAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Delete the current logged account from database
     */
    private function delete_account(): bool{
        $this->errno = 0;
        $user_password_hash = $this->uac_user->getPasswordHash();
        if(password_verify($this->password,$user_password_hash)){
            $user_id = $this->token->getUserId();
            $user_id_object = new ObjectId($user_id);
            if(!$this->action_list->actionlist_delete(['user_id' => $user_id_object])){
                 $this->errno = Dace::DELETE_USER;
                 return false;
            }
            if(!$this->article_list->articlelist_delete(['author' => $user_id_object])){
                $this->errno = Dace::DELETE_USER;
                 return false;
            }
            if(!$this->comment_list->commentlist_delete(['author' => $user_id_object])){
                $this->errno = Dace::DELETE_USER;
                 return false;
            }
            if(!$this->token->token_delete(['user_id' => $user_id_object])){
                $this->errno = Dace::DELETE_USER;
                 return false;
            }
            if(!$this->user->delete(['_id' => $user_id_object])){
                $this->errno = Dace::DELETE_USER;
                 return false;
            }
            return true;
        }//if(password_verify($this->password,$user_password_hash)){
        else $this->errno = Dace::CURRENT_PASSWORD_WRONG;
        return false;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = C::ACCOUNTDELETE_OK;
                break;
            case Dace::CURRENT_PASSWORD_WRONG:
                $this->response_code = 401;
                $this->response = Dace::CURRENT_PASSWORD_WRONG_MSG;
                break;
            case Dace::FROM_USERAUTHORIZEDCONTROLLER:
                $errnoUac = $this->uac->getErrno();
                switch($errnoUac){
                    case Uace::FROM_TOKEN;
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response_code = 401;
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response_code = 500;
                                $this->response = C::ACCOUNTDELETE_ERROR;
                                break;
                        }//switch($errnoT){
                        break;
                    case Uace::TOKEN_NOTFOUND:
                    case Uace::USER_NOTFOUND:
                        $this->response_code = 500;
                        $this->response = C::ACCOUNTDELETE_ERROR;
                        break;
                }//switch($errnoUac){
                break;
            case Dace::DELETE_USER:
            default:
                $this->response_code = 500;
                $this->response = C::ACCOUNTDELETE_ERROR;
                break;
        }
    }
}
?>