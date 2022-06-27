<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\DeleteControllerErrors as Dce;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Token;


class DeleteController implements Dce{
    private ?Article $article;
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
    }

    //Check if array provided has valid values
    private function checkValues(array $data){
        if(!isset($data['article']))throw new \Exception(Dce::NOARTICLEINSTANCE_EXC);
        if(!isset($data['token']))throw new \Exception(Dce::NOTOKENINSTANCE_EXC);
        if(!$data['article'] instanceof Article)throw new \Exception(Dce::INVALIDARTICLETYPE_EXC);
        if(!$data['token'] instanceof Token)throw new \Exception(Dce::INVALIDTOKENTYPE_EXC);
    }
}
?>