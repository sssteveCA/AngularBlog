<?php

namespace AngularBlog\Interfaces\MyArticles;

interface GetControllerErrors{
    //Exception
    const NOTOKENINSTANCE_EXC = "L'oggetto token è uguale a null";

    //Numbers
    const USERIDISNULL = 1;
    const NOARTICLESFOUND = 2;

    //Messages
    const USERIDISNULL_MSG = "La proprietà user_id è uguale a null";
    const NOARTICLESFOUND_MSG = "Non hai ancora creato nessun articolo";
}
?>