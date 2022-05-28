<?php

namespace AngularBlog\Interfaces\Login;

interface LoginControllerErrors{
    //Exceptions
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";

    //Numbers
    const USERNAMENOTFOUND = 1;
    const WRONGPASSWORD = 2;
    const ACCOUNTNOTACTIVATED = 3;

    //Messages
    const USERNAMENOTFOUND_MSG = "Lo username specificato non appartiene a nessun account";
    const WRONGPASSWORD_MSG = "La password inserita è errata";
    const ACCOUNTNOTACTIVATED_MSG = "Devi attivare il tuo account prima di accedere";
}
?>