<?php

namespace AngularBlog\Interfaces\Subscribe;

use AngularBlog\Interfaces\ExceptionMessages;

//Error constants of RegistrationController class
interface RegistrationControllerErrors extends ExceptionMessages{

    //numbers
    const MAILNOTSENT = 1;
    const FROMUSER = 2; //Error from User instance

    //messages
    const MAILNOTSENT_MSG = "Errore durante l' invio della mail";
    const FROMUSER_MSG = "Errore nell'oggetto User";
    
}
?>