<?php

namespace AngularBlog\Interfaces\Action;
use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface ActionAuthorizedControllerErrors extends ExceptionMessages, FromErrors{

    //Numbers
    const ACTION_NOTFOUND = 1; //No action found with passed id
    const TOKEN_NOTFOUND = 2; //No token found with passed token key
    const FORBIDDEN = 3; //Action editing is not allowed for current user

    //Messages
    const ACTION_NOTFOUND_MSG = "L'azione che stai cercando non esiste o è stata rimossa";
    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const FORBIDDEN_MSG = "Non sei autorizzato a modificare questa azione";
}
?>