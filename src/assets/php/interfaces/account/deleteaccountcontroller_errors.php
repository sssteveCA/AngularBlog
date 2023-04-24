<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface DeleteAccountControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const CURRENT_PASSWORD_WRONG = 1;
    const DELETE_USER = 2;
    const DELETE_ACTIONS = 3;
    const DELETE_ARTICLES = 4;
    const DELETE_COMMENTS = 5;
    const DELETE_TOKEN = 6;

    //Messages
    const CURRENT_PASSWORD_WRONG_MSG = "La password attuale non è corretta, riprova";
    const DELETE_USER_MSG = "Errore durante la rimozione dell'account nella collection utenti";
    const DELETE_ACTIONS_MSG = "Errore durante la rimozione delle azioni nella collection actions";
    const DELETE_ARTICLES_MSG = "Errore durante la rimozione degli articoli nella collection articles";
    const DELETE_COMMENTS_MSG = "Errore durante la rimozione dei commenti nella collection comments";
    const DELETE_TOKEN_MSG = "Errore durante la rimozione dell'account nella collection utenti";
}
?>