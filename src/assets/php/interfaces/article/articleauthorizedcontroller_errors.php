<?php

namespace AngularBlog\Interfaces\Article;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface ArticleAuthorizedControllerErrors extends ExceptionMessages, FromErrors{

    //Numbers
    const ARTICLE_NOTFOUND = 1; //No article found with passed id
    const TOKEN_NOTFOUND = 2; //No token found with passed token key
    const FORBIDDEN = 3; //Article editing is not allowed for current user

    //Messages
    const ARTICLE_NOTFOUND_MSG = "L'articolo che stai cercando non esiste o è stato rimosso";
    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const FORBIDDEN_MSG = "Non sei autorizzato a modificare questo articolo";
}
?>