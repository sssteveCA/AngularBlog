<?php

namespace AngularBlog\Interfaces\Action;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface GetUserActionControllerErrors extends ExceptionMessages, FromErrors{

    //Numbers
    const NOUSERIDFOUND = 1;
    const NOACTIONFOUND = 2;

     //Messages
     const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";
     const NOACTIONFOUND_MSG = "Non hai effettuato nessuna azione";

}
?>