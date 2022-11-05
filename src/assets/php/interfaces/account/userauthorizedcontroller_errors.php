<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface UserAuthorizedControllerErrors extends ExceptionMessages, FromErrors{
    const TOKEN_NOTFOUND = 1;
    const USER_NOTFOUND = 2;

    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
    const USER_NOTFOUND_MSG = "Nessun utente trovato con la chiave passata";
}
?>