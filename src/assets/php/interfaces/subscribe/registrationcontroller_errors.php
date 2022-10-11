<?php

namespace AngularBlog\Interfaces\Subscribe;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

//Error constants of RegistrationController class
interface RegistrationControllerErrors extends ExceptionMessages, FromErrors{

    //numbers
    const MAILNOTSENT = 1;

    //messages
    const MAILNOTSENT_MSG = "Errore durante l' invio della mail";
    
}
?>