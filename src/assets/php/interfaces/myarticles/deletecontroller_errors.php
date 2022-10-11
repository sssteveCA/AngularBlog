<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;

interface DeleteControllerErrors extends ExceptionMessages{
    //Numbers
    const FROM_ARTICLEAUTHORIZEDCONTROLLER = 1; //Error from ArticleAuthorizedController
    const ARTICLENOTDELETED = 2; //Article not deleted from Database

    //Messages
    const FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe ArticleAuthorizedController";
    const ARTICLENOTDELETED_MSG = "L'articolo non è stato rimosso";
}
?>