<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;

interface EditControllerErrors extends ExceptionMessages{
    //Numbers
    const FROM_ARTICLEAUTHORIZEDCONTROLLER = 1; //Error from ArticleAuthorizedController
    const ARTICLENOTUPDATED = 2; //Article information was not updated
    const PERMALINKDUPLICATE = 3; //The value that want insert of unique field already exists

    //Messages
    const FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe ArticleAuthorizedController";
    const ARTICLENOTUPDATED_MSG = "Le informazioni dell'articolo non sono state aggiornate";
    const PERMALINKDUPLICATE_MSG = "Il permalink inserito esiste già";
}
?>