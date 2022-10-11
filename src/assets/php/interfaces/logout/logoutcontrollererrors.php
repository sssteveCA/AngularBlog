<?php

namespace AngularBlog\Interfaces\Logout;

use AngularBlog\Interfaces\ExceptionMessages;

//Errors of class LogoutController
interface LogoutControllerErrors extends ExceptionMessages{

    //Numbers
    const TOKENNOTDELETED = 1; //Failed deleting the token

    //Messages
    const TOKENNOTDELETED_MSG = "Errore durante la cancellazione del token";
}
?>