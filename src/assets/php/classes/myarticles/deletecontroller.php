<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Interfaces\MyArticles\DeleteControllerErrors as Dce;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Article\ArticleAuthorizedController;
use AngularBlog\Classes\Token;
use MongoDB\BSON\ObjectId;


class DeleteController implements Dce{
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
            $del = $this->delete_article();
        }
        $this->setResponse();
    }

    public function getResponse(){return $this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $this->error = Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG;
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
        if(!isset($data['article']))throw new \Exception(Dce::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Dce::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Dce::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Dce::INVALIDTOKENTYPE_EXC);
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
        file_put_contents(DeleteController::$logFile,"DeleteController setResponse errno => {$this->errno}\r\n",FILE_APPEND);
        switch($this->errno){
            case 0:
                $this->response = C::ARTICLEDELETE_OK;
                break;
            case Dce::FROM_ARTICLEAUTHORIZEDCONTROLLER:
                $aacErrno = $this->aac->getErrno();
                switch($aacErrno){
                    case Aace::FROM_TOKEN:
                        $errnoT = $this->token->getErrno();
                        switch($errnoT){
                            case Te::TOKENEXPIRED:
                                $this->response = Te::TOKENEXPIRED_MSG;
                                break;
                            default:
                                $this->response = C::ARTICLEDELETE_ERROR;
                                break;
                        }
                        break;
                    case Aace::TOKEN_NOTFOUND:
                    case Aace::FORBIDDEN:
                        $this->response = Aace::FORBIDDEN_MSG;
                        break;
                    default:
                        $this->response = C::ARTICLEDELETE_ERROR;
                        break;
                }//switch($aacErrno){
                break;
            case Dce::ARTICLENOTDELETED:
            default:
                $this->response = C::ARTICLEDELETE_ERROR;
                break;
        }
    }
}
?>