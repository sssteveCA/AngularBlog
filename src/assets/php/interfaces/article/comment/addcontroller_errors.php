<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;

interface AddControllerErrors extends ExceptionMessages{
    //Numbers
    const FROM_ARTICLE = 1;
    const FROM_TOKEN = 2;
    const FROM_COMMENT = 3;
    const NOUSERIDFOUND = 4;

    //Messages
    const FROM_ARTICLE_MSG = "Errore dalla classe Article";
    const FROM_TOKEN_MSG = "Errore nella classe Token";
    const FROM_COMMENT_MSG = "Errore nella classe Comment";
    const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";
    

    
}

?>