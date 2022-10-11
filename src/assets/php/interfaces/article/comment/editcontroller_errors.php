<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;

interface EditControllerErrors extends ExceptionMessages{

   //Numbers
   const FROM_COMMENTAUTHORIZEDCONTROLLER = 1; //Error from CommentAuthorizedController
   const COMMENTNOTUPDATED = 2; //Comment information was not updated

   //Messages
   const FROM_COMMENTAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe CommentAuthorizedController";
   const COMMENTNOTUPDATED_MSG = "Le informazioni del commento non sono state aggiornate";
}
?>