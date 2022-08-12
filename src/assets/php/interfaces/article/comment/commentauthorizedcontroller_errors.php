<?php

namespace AngularBlog\Interfaces\Article\Comment;

interface CommentAuthorizedControllerErrors{
     //Exceptions
     const NOCOMMENTINSTANCE_EXC = "L'oggetto Comment passato è uguale a null";
     const NOTOKENINSTANCE_EXC = "L'oggetto Token passato è uguale a null";
     const COMMENTTYPEMISMATCH_EXC = "La variabile Comment non è del tipo atteso";
     const TOKENTYPEMISMATCH_EXC = "La variabile Token non è del tipo atteso";

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