<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface EditControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const ARTICLENOTUPDATED = 2; //Article information was not updated
    const PERMALINKDUPLICATE = 3; //The value that want insert of unique field already exists

    //Messages
    const ARTICLENOTUPDATED_MSG = "Le informazioni dell'articolo non sono state aggiornate";
    const PERMALINKDUPLICATE_MSG = "Il permalink inserito esiste già";
}
?>