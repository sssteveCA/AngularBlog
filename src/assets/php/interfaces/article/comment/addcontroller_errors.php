<?php

namespace AngularBlog\Interfaces\Article\Comment;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface AddControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const NOUSERIDFOUND = 4;

    //Messages
    const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";
    

    
}

?>