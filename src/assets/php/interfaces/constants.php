<?php

namespace AngularBlog\Interfaces;

//Generic constants used by this app
interface Constants{

    //Activation
    const ACTIVATION_INVALID_CODE = "Codice non valido";
    const ACTIVATION_OK = "L' account è stato attivato con successo";
    const ACTIVATION_ERROR = "Errore durante l'ativazione dell'account. ".Constants::ADMIN_CONTACT;

    //Article
    const ARTICLECREATION_ERROR = "Errore durante la creazione dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLEEDITING_ERROR = "Errore durante la modifica dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLEEDITING_OK = "";

    //Cookie
    const COOKIE_ID = 'id';
    const COOKIE_NAME = 'username';

    //Email
    const EMAIL_ACCOUNT_CREATED = 'Account creato con successo. Per completare la registrazione accedi alla tua casella di posta';
    const EMAIL_ACTIVATION_SUBJECT = 'Attivazione account';
    const EMAIL_ERROR = "Errore durante l'invio della mail. ".Constants::ADMIN_CONTACT;

    //Generic errors
    const ERROR_UNKNOWN = 'Errore sconosciuto';
    const ERROR_CONFIRM_PASSWORD_DIFFERENT = 'Le due password non coincidono';
    const FILL_ALL_FIELDS = 'Inserisci tutti i dati richiesti per continuare';

    //File
    const FILE_LOG = "./log.txt";

    //Login
    const LOGIN_ERROR = "Errore durante il login. ".Constants::ADMIN_CONTACT;
    const LOGOUT_ERROR = "Errore durante il logout. ".Constants::ADMIN_CONTACT;
    const LOGOUT_ERROR_USERNOTFOUND = "Impossibile completare il logout perché la chiave passata non è valida";

    //MongoDB
    const MONGODB_CONNECTION_STRING = "mongodb://localhost:27017";
    const MONGODB_DATABASE = 'AngularBlog';
    const MONGODB_COLLECTION_ARTICLES = 'articles';
    const MONGODB_COLLECTION_TOKENS = 'tokens';
    const MONGODB_COLLECTION_USERS = 'users';

    //Registration
    const REG_ERROR = "Errore durante la registrazione dell' account. ".Constants::ADMIN_CONTACT;
    const REG_SUBSCRIBE_LINK = 'http://localhost:4200/attiva';

    //Search
    const SEARCH_ERROR = "Errore durante la ricerca degli articoli. ".Constants::ADMIN_CONTACT;

    //Other
    const ADMIN_CONTACT = "Se il problema persiste contattare l' amministratore del sito";
}
?>