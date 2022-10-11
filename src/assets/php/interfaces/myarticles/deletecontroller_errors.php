<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface DeleteControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const ARTICLENOTDELETED = 2; //Article not deleted from Database

    //Messages
    const ARTICLENOTDELETED_MSG = "L'articolo non è stato rimosso";
}
?>