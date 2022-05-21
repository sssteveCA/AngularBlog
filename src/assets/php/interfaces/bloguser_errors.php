<?php

namespace AngularBlog\Interfaces;

//This interface contains error constants of BlogUser class
interface BlogUserErrors{
    //Exceptions
    const EXC_NOCOLLECTION = "La collection richiesta non è stata trovata";
    const EXC_NODATABASE = "Il database richiesto non è stato trovato";

    //Numbers
    const INVALIDDATAFORMAT = 1;
    const USERNAMEMAILEXIST = 2;

    //Messages
    const INVALIDDATAFORMAT_MSG = "";
    const USERNAMEMAILEXIST_MSG = "";
}
?>