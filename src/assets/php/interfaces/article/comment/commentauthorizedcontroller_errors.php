<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;

interface CommentAuthorizedControllerErrors extends ExceptionMessages{

     //Numbers
    const COMMENT_NOTFOUND = 1; //No comment found with passed id
    const TOKEN_NOTFOUND = 2; //No token found with passed token key
    const FORBIDDEN = 3; //Comment editing is not allowed for current user
    const FROM_TOKEN = 4; //Error from Token class

    //Messages
    const COMMENT_NOTFOUND_MSG = "Il commento che stai cercando non esiste o è stato rimosso";
    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const FORBIDDEN_MSG = "Non sei autorizzato a modificare questo commento";
    const FROM_TOKEN_MSG = "Errore nella classe Token";
}
?>