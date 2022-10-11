<?php

namespace AngularBlog\Interfaces\Subscribe;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface VerifyControllerErrors extends ExceptionMessages, FromErrors{

    //Numbers
    const DATANOTSET = 1;

    //Messages
    const DATANOTSET_MSG = "I dati richiesti non sono stati impostati";
}
?>