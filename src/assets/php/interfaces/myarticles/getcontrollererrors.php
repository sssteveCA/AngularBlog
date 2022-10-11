<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;

interface GetControllerErrors extends ExceptionMessages{

    //Numbers
    const NOUSERIDFOUND = 1;
    const NOARTICLESFOUND = 2;

    //Messages
    const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";
    const NOARTICLESFOUND_MSG = "Non è stato trovato nessun articolo creato da te";
}
?>