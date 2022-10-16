<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface UserAuthorizedControllerErrors extends ExceptionMessages, FromErrors{
    const TOKEN_NOTFOUND = 1;

    const TOKEN_NOTFOUND_MSG = "Non è stato trovato nessun token con la chiave passata";
}
?>