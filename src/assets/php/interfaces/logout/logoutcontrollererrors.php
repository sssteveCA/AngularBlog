<?php

namespace AngularBlog\Interfaces\Logout;

//Errors of class LogoutController
interface LogoutControllerErrors{
    //Exceptions
    const NOTOKENINSTANCE_EXC = "L'oggetto Token passato è uguale a null";

    //Numbers
    const TOKENNOTDELETED = 1; //Failed deleting the token

    //Messages
    const TOKENNOTDELETED_MSG = "Errore durante la cancellazione del token";
}
?>