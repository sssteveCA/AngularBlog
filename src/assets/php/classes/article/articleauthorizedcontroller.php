<?php

namespace AngularBlog\Classes\Article;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Interfaces\TokenErrors as Te;
use AngularBlog\Interfaces\Article\ArticleAuthorizedControllerErrors as Aace;
use AngularBlog\Classes\Token;
use AngularBlog\Exceptions\ArticleTypeMismatchException;
use AngularBlog\Exceptions\NoArticleInstanceException;
use AngularBlog\Exceptions\NoTokenInstanceException;
use AngularBlog\Exceptions\TokenTypeMismatchException;
use AngularBlog\Traits\AuthorizedTrait;
use MongoDB\BSON\ObjectId;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

//Check if user is authorized to do write operation with a certain article
class ArticleAuthorizedController implements Aace{

    use ErrorTrait, ResponseTrait, AuthorizedTrait;

    private ?Article $article;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data){
        //file_put_contents(ArticleAuthorizedController::$logFile,"ArticleAuthorizedController construct\r\n",FILE_APPEND);
        $this->checkValues($data);
        $this->article = $data['article'];
        $this->token = $data['token'];
        $tokenOk = $this->getTokenByKey();
        if($tokenOk){
            //Token exists
            $articleOk = $this->getArticleById();
            if($articleOk){
                //Article exists
                $authOk = $this->isUserAuthorizedCheck();
            }
        }
        //file_put_contents(ArticleAuthorizedController::$logFile,var_export($this->errno,true)."\r\n",FILE_APPEND);
        $this->setResponse();
    }

    public function getArticle(){return $this->article;}
    public function getToken(){return $this->token;}
    public function getError(){
        switch($this->errno){
            case Aace::ARTICLE_NOTFOUND:
                $this->error = Aace::ARTICLE_NOTFOUND_MSG;
                break;
            case Aace::TOKEN_NOTFOUND:
                $this->error = Aace::TOKEN_NOTFOUND_MSG;
                break;
            case Aace::FORBIDDEN:
                $this->error = Aace::FORBIDDEN_MSG;
                break;
            case Aace::FROM_TOKEN:
                $this->error = Aace::FROM_TOKEN_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    /**
     * Check if values inside array are Article and Token types
     */
    private function checkValues(array $data){
        if(!isset($data['article']))throw new NoArticleInstanceException(Aace::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new NoTokenInstanceException(Aace::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new ArticleTypeMismatchException(Aace::ARTICLETYPEMISMATCH_EXC);
        if(!$data['token'] instanceof Token)throw new TokenTypeMismatchException(Aace::TOKENTYPEMISMATCH_EXC);
    }

    /**
     * Get token by token key
     */
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
                $this->errno = Aace::FROM_TOKEN;
            }
            else
                $got = true;
        }
        else
            $this->errno = Aace::TOKEN_NOTFOUND;
        return $got;
    }

    /**
     * Get article info by id
     */
    private function getArticleById(): bool{
        $got = false;
        $this->errno = 0;
        $article_id = $this->article->getId();
        file_put_contents(ArticleAuthorizedController::$logFile,"getArticle article id => ".var_export($article_id,true)."\r\n",FILE_APPEND);
        $data = ['_id' => new ObjectId($article_id)];
        $article_got = $this->article->article_get($data);
        if($article_got){
            $got = true;
        }
        else
            $this->errno = Aace::ARTICLE_NOTFOUND;
        return $got;
    }

    /**
     * Check if user is authorized to edit this article
     */
    private function isUserAuthorizedCheck(): bool{
        $this->authorized = false;
        $this->errno = 0;
        $user_id = $this->token->getUserId();
        $article_author = $this->article->getAuthor();
        if($user_id == $article_author){
            //User is the owner of the article
            $this->authorized = true;
        }
        else
            $this->errno = Aace::FORBIDDEN;
        return $this->authorized;
    }

    /**
     * Set the response to send to the view
     */
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response_code = 200;
                $this->response = "OK";
                break;
            case Aace::FROM_TOKEN:
                $errnoT = $this->token->getErrno();
                switch($errnoT){
                    case Te::TOKENEXPIRED:
                        $this->response_code = 401;
                        $this->response = Te::TOKENEXPIRED_MSG;
                        break;
                    default:
                        $this->response_code = 500;
                        $this->response = C::ERROR_UNKNOWN;
                        break;
                }//switch($errnoT){
                break;
            case Aace::TOKEN_NOTFOUND:
            case Aace::FORBIDDEN:
                $this->response_code = 403;
                $this->response = Aace::FORBIDDEN_MSG;
                break;
            case Aace::ARTICLE_NOTFOUND:
                $this->response_code = 404;
                $this->response = Aace::ARTICLE_NOTFOUND_MSG;
                break;
            default:
                $this->response_code = 500;
                $this->response = C::ERROR_UNKNOWN;
                break;
        }
    }




}
?>