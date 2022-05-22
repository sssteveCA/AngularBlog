<?php

namespace AngularBlog\Interfaces;

//Generic constants used by this app
interface Constants{

    //Email
    const EMAIL_ACCOUNT_CREATED = 'Account creato con successo. Per completare la registrazione accedi alla tua casella di posta';
    const EMAIL_ACTIVATION_SUBJECT = 'Attivazione account';

    //Generic errors
    const ERROR_UNKNOWN = 'Errore sconosciuto';
    const ERROR_CONFIRM_PASSWORD_DIFFERENT = 'Le due password non coincidono';
    const FILL_ALL_FIELDS = 'Inserisci tutti i dati richiesti per continuare';

    //File
    const FILE_LOG = "./log.txt";

    //MongoDB
    const MONGODB_CONNECTION_STRING = "mongodb://localhost:27017/";
    const MONGODB_DATABASE = 'AngularBlog';
    const MONGODB_COLLECTION_USERS = 'users';

    //Registration
    const REG_ERROR = "Errore durante la registrazione dell\' account";
    const REG_SUBSCRIBE_LINK = 'http://localhost:4200/attiva';
}
?>