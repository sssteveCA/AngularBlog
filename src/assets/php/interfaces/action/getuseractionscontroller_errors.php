<?php

namespace AngularBlog\Interfaces\Action;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface GetUserActionControllerErrors extends ExceptionMessages, FromErrors{

    //Numbers
    const NOUSERIDFOUND = 1;

     //Messages
     const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";

}
?>