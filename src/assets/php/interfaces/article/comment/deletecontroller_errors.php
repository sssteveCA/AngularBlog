<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;

interface DeleteControllerErrors extends ExceptionMessages{
    //Numbers
    const FROM_COMMENTAUTHORIZEDCONTROLLER = 1; //Error from CommentAuthorizedController
    const COMMENTNOTDELETED = 2; //Comment not deleted from Database

    //Messages
    const FROM_COMMENTAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe CommentAuthorizedController";
    const COMMENTNOTDELETED_MSG = "Il commento non è stato rimosso";
}

?>