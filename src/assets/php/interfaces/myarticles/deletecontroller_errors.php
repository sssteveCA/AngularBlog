<?php

namespace AngularBlog\Interfaces\MyArticles;

interface DeleteControllerErrors{
    //Exceptions
    const NOARTICLEINSTANCE_EXC = "L'oggetto Article è uguale a null";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token è uguale a null";
    const INVALIDARTICLETYPE_EXC = "L'articolo fornito non è in un formato valido";
    const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido";

    //Numbers
    const FROM_ARTICLEAUTHORIZEDCONTROLLER = 1; //Error from ArticleAuthorizedController
    const ARTICLENOTDELETED = 2; //Article not deleted from Database

    //Messages
    const FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe ArticleAuthorizedController";
    const ARTICLENOTDELETED_MSG = "L'articolo non è stato rimosso";
}
?>