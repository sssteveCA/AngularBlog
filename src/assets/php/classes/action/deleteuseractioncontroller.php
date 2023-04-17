<?php


use AngularBlog\Classes\Action\Action;
use AngularBlog\Classes\Action\ActionAuthorizedController;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ActionTypeMismatchException;
use AngularBlog\Exceptions\NoActionInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Action\DeleteUserActionControllerErrors as Duace;
use AngularBlog\Interfaces\Action\ActionAuthorizedControllerErrors as Aace;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;
use MongoDB\BSON\ObjectId;

class DeleteUserActionController implements Duace{

    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private ?Action $action;
    private ?Action $aac_action;
    private ?ActionAuthorizedController $aac;

    public function __construct(array $data){
        $this->checkValues($data);
        if($this->checkAuthorization()){
            $this->deleteAction();
        }
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Duace::FROM_ACTIONAUTHORIZEDCONTROLLER:
                $this->error = Duace::FROM_ACTIONAUTHORIZEDCONTROLLER_MSG;
                break;
            case Duace::FROM_ACTION:
                $this->error = Duace::FROM_ACTION_MSG;
                break;
            case Duace::ACTIONNOTDELETED:
                $this->error = Duace::ACTIONNOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
    }

    /**
     * Check if array provided has valid values
     */
    private function checkValues(array $data){
        if(!isset($data['action']))throw new NoActionInstanceException(Duace::NOACTIONINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Duace::NOTOKENINSTANCE_EXC);
        if(!$data['action'] instanceof Action) throw new ActionTypeMismatchException(Duace::INVALIDACTIONTYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Duace::INVALIDTOKENTYPE_EXC);
        $this->action = $data['action'];
        $this->token = $data['token'];
    }

    /**
     * Check if user is authorized to manage this action
     */
    private function checkAuthorization(): bool{
        $this->errno = 0;
        $this->aac_action = clone $this->action;
        $this->aac = new ActionAuthorizedController([
            'action' => $this->action,
            'token' => $this->token
        ]);
        $aacErrno = $this->aac->getErrno();
        if($aacErrno == 0)
            return true;
        $this->errno = Duace::FROM_ACTIONAUTHORIZEDCONTROLLER;
        return false;
    }

    /**
     * Delete this action
     */
    private function deleteAction(): bool{
        $this->errno = 0;
        $action_id = $this->action->getId();
        $filter = ['_id' => new ObjectId($action_id)];
        $action_delete = $this->action->action_delete($filter);
        if($action_delete)
            return true;
        $this->errno = Duace::ACTIONNOTDELETED;
        return false;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        switch($this->errno){
           case 0:
                $this->response_code = 200;
                $this->response = C::HISTORYITEM_DELETE_OK;
                break; 
            case Duace::FROM_ACTIONAUTHORIZEDCONTROLLER:
                $aacErrno = $this->aac->getErrno();
                switch($aacErrno){
                    case Aace::FROM_TOKEN:
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response_code = 401;
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response_code = 500;
                                $this->response = C::HISTORYITEM_DELETE_ERROR;
                                break;
                        }
                        break;
                        case Aace::TOKEN_NOTFOUND:
                        case Aace::FORBIDDEN:
                            $this->response_code = 403;
                            $this->response = Aace::FORBIDDEN_MSG;
                            break;
                        default:
                            $this->response_code = 500;
                            $this->response = C::HISTORYITEM_DELETE_ERROR;
                            break;
                }//switch($aacErrno){
                break;
            case Duace::ACTIONNOTDELETED:
            default:
                    $this->response_code = 500;
                    $this->response = C::HISTORYITEM_DELETE_ERROR;
                    break;
        
        }
        
    }

}

?>