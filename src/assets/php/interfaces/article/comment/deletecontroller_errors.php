<?php

namespace AngularBlog\Interfaces\Article\Comment;

interface DeleteControllerErrors{
    //Exceptions
    const NOCOMMENTINSTANCE_EXC = "L'oggetto Comment è uguale a null";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token è uguale a null";
    const INVALIDCOMMENTTYPE_EXC = "Il commento fornito non è in un formato valido";
    const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido";

    //Numbers
    const FROM_COMMENTAUTHORIZEDCONTROLLER = 1; //Error from CommentAuthorizedController
    const COMMENTNOTDELETED = 2; //Comment not deleted from Database

    //Messages
    const FROM_COMMENTAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe CommentAuthorizedController";
    const COMMENTNOTDELETED_MSG = "Il commento non è stato rimosso";
}

?>