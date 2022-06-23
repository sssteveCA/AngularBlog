<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\MyArticles\EditControllerErrors as Ece;
use AngularBlog\Classes\Article\Article;
use AngularBlog\Classes\Token;

class EditContoller implements Ece,C{
    private ?Article $article;
    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        
    }
}
?>