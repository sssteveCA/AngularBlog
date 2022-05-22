<?php

namespace AngularBlog\Interfaces;

//Model class error constants
interface ModelErrors{
    //exceptions
    const CONNECTION_URL_EXC = "Nessuna stringa di connessione al database specificata";
    const DATABASE_NAME_EXC = "Nessun database a cui collegarsi specificato";
    const COLLECTION_NAME_EXC = "Nessuna collezione specificata";

    //number
    const NORESULT = 1; //No result from get operation
    const NOTCREATED = 2; //Document not inserted
    const NOTUPDATED = 3; //Document not updated
    const NOTDELETED = 4; //Document not deleted

    //messages
    const NORESULT_MSG = "Nessun documento restituito";
    const NOTCREATED_MSG = "Nessun documento creato";
    const NOTUPDATED_MSG = "Nessun documento aggiornato";
    const NOTDELETED_MSG = "Nessun documento eliminato";

    //other
    const RANGE_MIN = 0; //minimum error code of Model class
    const RANGE_MAX = 20; //maximum error code of Model class
}
?>