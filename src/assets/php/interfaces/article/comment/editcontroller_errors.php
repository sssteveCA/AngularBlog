<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface EditControllerErrors extends ExceptionMessages, FromErrors{

   //Numbers
   const COMMENTNOTUPDATED = 2; //Comment information was not updated

   //Messages
   const COMMENTNOTUPDATED_MSG = "Le informazioni del commento non sono state aggiornate";
}
?>