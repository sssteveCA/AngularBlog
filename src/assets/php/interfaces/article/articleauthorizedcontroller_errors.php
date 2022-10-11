<?php

namespace AngularBlog\Interfaces\Article;

use AngularBlog\Interfaces\ExceptionMessages;

interface ArticleAuthorizedControllerErrors extends ExceptionMessages{

    //Numbers
    const ARTICLE_NOTFOUND = 1; //No article found with passed id
    const TOKEN_NOTFOUND = 2; //No token found with passed token key
    const FORBIDDEN = 3; //Article editing is not allowed for current user
    const FROM_TOKEN = 4; //Error from Token class

    //Messages
    const ARTICLE_NOTFOUND_MSG = "L'articolo che stai cercando non esiste o è stato rimosso";
    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const FORBIDDEN_MSG = "Non sei autorizzato a modificare questo articolo";
    const FROM_TOKEN_MSG = "Errore nella classe Token";
}
?>