<?php

namespace AngularBlog\Interfaces\Subscribe;

interface VerifyControllerErrors{
    //Exception
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";

    //Numbers
    const DATANOTSET = 1;
    const FROMUSER = 2; //Error from User instance

    //Messages
    const DATANOTSET_MSG = "I dati richiesti non sono stati impostati";
    const FROMUSER_MSG = "Errore nell'oggetto User";
}
?>