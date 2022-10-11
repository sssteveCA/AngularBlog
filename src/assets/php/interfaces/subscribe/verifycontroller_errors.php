<?php

namespace AngularBlog\Interfaces\Subscribe;

use AngularBlog\Interfaces\ExceptionMessages;

interface VerifyControllerErrors extends ExceptionMessages{

    //Numbers
    const DATANOTSET = 1;
    const FROMUSER = 2; //Error from User instance

    //Messages
    const DATANOTSET_MSG = "I dati richiesti non sono stati impostati";
    const FROMUSER_MSG = "Errore nell'oggetto User";
}
?>