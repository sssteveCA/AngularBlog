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
    const MAILNOTSENT = 3;
    const ACCOUNTNOTACTIVATED = 4;
    const DATANOTSET = 5;

    //Messages
    const INVALIDDATAFORMAT_MSG = "I dati sono in un formato non valido";
    const USERNAMEMAILEXIST_MSG = "Lo username o l'indirizzo email specificato esistono già";
    const MAILNOTSENT_MSG = "Errore durante l' invio della mail.";
    const ACCOUNTNOTACTIVATED_MSG = "";
    const DATANOTSET_MSG = "";
}
?>