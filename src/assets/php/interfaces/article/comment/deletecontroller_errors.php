<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface DeleteControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const COMMENTNOTDELETED = 2; //Comment not deleted from Database

    //Messages
    const COMMENTNOTDELETED_MSG = "Il commento non è stato rimosso";
}

?>