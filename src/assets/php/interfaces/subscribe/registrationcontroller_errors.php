<?php

namespace AngularBlog\Interfaces\Subscribe;

//Error constants of RegistrationController class
interface RegistrationControllerErrors{

    //Exceptions
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";

    //numbers
    const MAILNOTSENT = 1;
    const FROMUSER = 2; //Error from User instance

    //messages
    const MAILNOTSENT_MSG = "Errore durante l' invio della mail";
    const FROMUSER_MSG = "Errore nell'oggetto User";
    
}
?>