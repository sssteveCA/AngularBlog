<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface DeleteAccountControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const CURRENT_PASSWORD_WRONG = 1;
    const DELETE_USER = 2;

    //Messages
    const CURRENT_PASSWORD_WRONG_MSG = "La password attuale non è corretta, riprova";
    const DELETE_USER_MSG = "Errore durante la rimozione dell'account nella collection utenti";
}
?>