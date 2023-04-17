<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Action\Action;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Interfaces\MyArticles\DeleteControllerErrors as Dce;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ArticleTypeMismatchException;
use AngularBlog\Exceptions\NoArticleInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use MongoDB\BSON\ObjectId;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class DeleteController implements Dce{

    use ErrorTrait, ResponseTrait;

    private ?Article $article;
    private ?Article $aac_article; //Article used by ArticleAuthorizationController class
    private ?Token $token;
    private ?Action $action;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        if($this->checkAuthorization()){
            if($this->delete_article())
                $this->addAction();
        }
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $this->error = Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG;
                break;
            case Dce::FROM_ACTION:
                $this->error = Dce::FROM_ACTION_MSG;
                break;
            case Dce::ARTICLENOTDELETED:
                $this->error = Dce::ARTICLENOTDELETED_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['article']))throw new NoArticleInstanceException(Dce::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Dce::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new ArticleTypeMismatchException(Dce::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Dce::INVALIDTOKENTYPE_EXC);
    }

    /**
     * Add an action to rememeber the done operation
     */
    private function addAction(): bool{
        $this->action = new Action([
            'user_id' => $this->token->getUserId(),
            'title' => 'Cancellazione articolo',
            'description' => <<<HTML
Hai eliminato l'articolo {$this->aac_article->getTitle()}
HTML
        ]);
        $insert = $this->action->action_create();
        if(!$insert) $this->errno = Dce::FROM_ACTION;
        return true;
    }

    //Check if user is authorized to edit the article
    private function checkAuthorization(): bool{
        $authorized = false;
        $this->errno = 0;
        $this->aac_article = clone $this->article;
        $aac = new ArticleAuthorizedController([
            'article' => $this->aac_article,
            'token' => $this->token
        ]);
        $aacErrno = $aac->getErrno();
        if($aacErrno == 0){
            $authorized = true;
        }
        else
            $this->errno = Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER;
        return $authorized;
    }

    //Delete the article
    private function delete_article(): bool{
        $del = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        file_put_contents(DeleteController::$logFile,"DeleteController delete_article article id => {$article_id}\r\n",FILE_APPEND);
        $filter = ['_id' => new ObjectId($article_id)];
        $article_delete = $this->article->article_delete($filter);
        if($article_delete)
            $del = true;
        else{
            $this->errno = Dce::ARTICLENOTDELETED;
        }
        return $del;
    }

    //Set the response to send to the view
    private function setResponse(){
        //file_put_contents(DeleteController::$logFile,"DeleteController setResponse errno => {$this->errno}\r\n",FILE_APPEND);
        switch($this->errno){
            case 0:
            case Dce::FROM_ACTION:
                $this->response_code = 200;
                $this->response = C::ARTICLEDELETE_OK;
                break;
            case Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER:
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
                                $this->response = C::ARTICLEDELETE_ERROR;
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
                        $this->response = C::ARTICLEDELETE_ERROR;
                        break;
                }//switch($aacErrno){
                break;
            case Dce::ARTICLENOTDELETED:
            default:
                $this->response_code = 500;
                $this->response = C::ARTICLEDELETE_ERROR;
                break;
        }
    }
}
?>