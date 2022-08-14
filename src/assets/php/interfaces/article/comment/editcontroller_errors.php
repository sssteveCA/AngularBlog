<?php

namespace AngularBlog\Interfaces\Article\Comment;

interface EditControllerErrors{
   //Exceptions
   const NOACOMMENTINSTANCE_EXC = "L'oggetto Comment è uguale a null";
   const NOTOKENINSTANCE_EXC = "L'oggetto Token è uguale a null";
   const INVALIDCOMMENTTYPE_EXC = "Il commento fornito non è in un formato valido";
   const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido"; 

   //Numbers
   const FROM_COMMENTAUTHORIZEDCONTROLLER = 1; //Error from CommentAuthorizedController
   const COMMENTNOTUPDATED = 2; //Comment information was not updated

   //Messages
   const FROM_COMMENTAUTHORIZEDCONTROLLER_MSG = "Errore dalla classe CommentAuthorizedController";
   const COMMENTNOTUPDATED_MSG = "Le informazioni del commento non sono state aggiornate";
}
?>