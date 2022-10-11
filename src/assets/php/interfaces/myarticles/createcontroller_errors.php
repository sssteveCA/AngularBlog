<?php

namespace AngularBlog\Interfaces\MyArticles;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface CreateControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const NOUSERIDFOUND = 1;
    const INVALIDARTICLEDATA = 2;
    const FROMARTICLE = 3; //Error from Article class
    const DUPLICATEDPERMALINK = 4; //Duplicated value in unique field 'permalink'

     //Messages
     const NOUSERIDFOUND_MSG = "Nessun id utente con la chiave di login fornita";
     const INVALIDARTICLEDATA_MSG = "I dati dell'articolo non sono formattati in modo corretto";
     const FROMARTICLE_MSG = "Errore dalla classe Article";
     const DUPLICATEDPERMALINK_MSG = "Il permalink fornito esiste già";
}
?>