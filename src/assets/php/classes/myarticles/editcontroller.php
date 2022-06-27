<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Token;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use MongoDB\BSON\ObjectId;

class EditContoller implements Ece{
    private ?Article $article;
    private ?Article $aac_article; //Article used by ArticleAuthorizationController class
    private ?ArticleAuthorizedController $aac;
    private ?Token $token;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        $auth = $this->checkAuthorization();
        if($auth){
            $edit = $this->edit_article();
        }
        file_put_contents(EditContoller::$logFile,"Edit Controller => ".var_export($this->getError(),true)."\r\n",FILE_APPEND);
        $this->setResponse();
    }

    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $this->error = Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG;
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
        if(!isset($data['article']))throw new \Exception(Ece::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Ece::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Ece::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Ece::INVALIDTOKENTYPE_EXC);

    }

    //Check if user is authorized to edit the article
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

    //Update article information
    private function edit_article(): bool{
        file_put_contents(EditContoller::$logFile,"Edit Controller edit Article\r\n",FILE_APPEND);
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

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = C::ARTICLEEDITING_OK;
                break;
            case Ece::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $aacErrno = $this->aac->getErrno();
                switch($aacErrno){
                    case Aace::TOKEN_NOTFOUND:
                    case Aace::FORBIDDEN:
                        $this->response = Aace::FORBIDDEN_MSG;
                        break;
                    default:
                        $this->response = C::ARTICLEEDITING_ERROR;
                        break;
                }//switch($aacErrno){
                break;
            case Ece::PERMALINKDUPLICATE:
                $this->response = $this->getError();
                break;
            case Ece::ARTICLENOTUPDATED:
            default:
                $this->response = C::ARTICLEEDITING_ERROR;
                break;
        }
    }
}
?>