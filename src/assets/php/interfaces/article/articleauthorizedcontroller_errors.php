<?php

namespace AngularBlog\Interfaces\Article;

interface ArticleAuthorizedControllerErrors{
    //Exceptions
    const NOARTICLEINSTANCE_EXC = "L'oggetto Article passato è uguale a null";
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token passato è uguale a null";
    const ARTICLETYPEMISMATCH_EXC = "La variabile Article non è del tipo atteso";
    const USERTYPEMISMATCH_EXC = "La variabile User non è del tipo atteso";
    const TOKENTYPEMISMATCH_EXC = "La variabile Token non è del tipo atteso";

    //Numbers
    const ARTICLE_NOTFOUND = 1; //No article found with passed id
    const USER_NOTFOUND = 2; //No user found with passed user_id  from token
    const TOKEN_NOTFOUND = 3; //No token found with passed token key
    const FORBIDDEN = 4; //Article editing is not allowed for current user

    //Messages
    const ARTICLE_NOTFOUND_MSG = "Non è stato trovato nessun articolo con l'id fornito";
    const USER_NOTFOUND_MSG = "Non è stato trovato nessun articolo con l'id fornito";
    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const FORBIDDEN_MSG = "Non sei autorizzato a modificare questo articolo";
}
?>