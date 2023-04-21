<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Action\Action;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\EditControllerErrors as Ece;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Exceptions\ArticleTypeMismatchException;
use AngularBlog\Exceptions\NoArticleInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use MongoDB\BSON\ObjectId;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class EditContoller implements Ece{

    use ErrorTrait, ResponseTrait;

    private ?Article $article;
    private ?Article $aac_article; //Article used by ArticleAuthorizationController class
    private ?ArticleAuthorizedController $aac;
    private ?Token $token;
    private ?Action $action;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        if($this->checkAuthorization()){
            if($this->edit_article())
                $this->addAction();
        }
        $this->setResponse();
    }

    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $this->error = Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG;
                break;
            case Ece::FROM_ACTION:
                $this->error = Ece::FROM_ACTION_MSG;
                break;
            case Ece::ARTICLENOTUPDATED:
                $this->error = Ece::ARTICLENOTUPDATED_MSG;
                break;
            case Ece::PERMALINKDUPLICATE:
                $this->error = Ece::PERMALINKDUPLICATE_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['article']))throw new NoArticleInstanceException(Ece::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new ArticleTypeMismatchException(Ece::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Ece::INVALIDTOKENTYPE_EXC);

    }

    /**
     * Add an action to rememeber the done operation
     */
    private function addAction(): bool{
        $this->action = new Action([
            'user_id' => $this->token->getUserId(),
            'title' => 'Modifica articolo',
            'description' => <<<HTML
Hai modificato l'articolo "{$this->aac_article->getTitle()}""
HTML
        ]);
        $insert = $this->action->action_create();
        if(!$insert) $this->errno = Ece::FROM_ACTION;
        return true;
    }

    /**
     * Check if user is authorized to edit the article
     **/
    private function checkAuthorization(): bool{
        $authorized = false;
        $this->errno = 0;
        $this->aac_article = clone $this->article;
        $this->aac = new ArticleAuthorizedController([
            'article' => $this->aac_article,
            'token' => $this->token
        ]);
        $aacErrno = $this->aac->getErrno();
        if($aacErrno == 0){
            $authorized = true;
        }
        else
            $this->errno = Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER;
        return $authorized;
    }

    /**
     * Update article information
     **/
    private function edit_article(): bool{
        $edited = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        $filter = ['_id' => new ObjectId($article_id)];
        $values = ['$set' => [
            'title' => $this->article->getTitle(),
            'author' => new ObjectId($this->token->getUserId()),
            'introtext' => $this->article->getTitle(),
            'content' => $this->article->getContent(),
            'permalink' => $this->article->getPermalink(),
            'categories' => $this->article->getCategories(),
            'tags' => $this->article->getTags()
        ]];
        $article_edit = $this->article->article_update($filter,$values);
        if($article_edit)
            $edited = true;
        else
            $this->errno = Ece::ARTICLENOTUPDATED;
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
                $this->response = C::ARTICLEEDITING_OK;
                break;
            case Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER:
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
                                $this->response = C::ARTICLEEDITING_ERROR;
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
                        $this->response = C::ARTICLEEDITING_ERROR;
                        break;
                }//switch($aacErrno){
                break;
            case Ece::PERMALINKDUPLICATE:
                $this->response_code = 400;
                $this->response = $this->getError();
                break;
            case Ece::ARTICLENOTUPDATED:
            default:
                $this->response_code = 500;
                $this->response = C::ARTICLEEDITING_ERROR;
                break;
        }
    }
}
?>