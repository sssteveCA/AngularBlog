<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface UpdatePasswordControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const CURRENT_PASSWORD_WRONG = 1;
    const UPDATE_USER = 2;

    //Messages
    const CURRENT_PASSWORD_WRONG_MSG = "La password attuale non è corretta, riprova";
    const UPDATE_USER_MSG = "Errore durante la modifica nella collection utenti";
}
?>