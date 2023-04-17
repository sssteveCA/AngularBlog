<?php

namespace AngularBlog\Interfaces;

//Generic constants used by this app
interface Constants{

    const ADMINMAIL = "admin@AngularBlog.com";

    //Account delete
    const ACCOUNTDELETE_ERROR = "Impossibile eliminare il tuo account. ".Constants::ADMIN_CONTACT;
    const ACCOUNTDELETE_OK = "Il tuo account è stato eliminato definitivamente";

    //Activation
    const ACTIVATION_INVALID_CODE = "Codice non valido";
    const ACTIVATION_OK = "L' account è stato attivato con successo";
    const ACTIVATION_ERROR = "Errore durante l'ativazione dell'account. ".Constants::ADMIN_CONTACT;

    //Article
    const ARTICLECREATION_ERROR = "Errore durante la creazione dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLEDELETE_ERROR = "Errore durante la rimozione dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLEDELETE_OK = "L'articolo è stato rimosso con successo";
    const ARTICLEEDITING_ERROR = "Errore durante la modifica dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLEEDITING_OK = "L'articolo è stato modificato con successo";
    const ARTICLEVIEW_ERROR = "Errore durante la visualizzazione dell'articolo. ".Constants::ADMIN_CONTACT;
    const ARTICLESVIEW_ERROR = "Errore durante la ricerca degli articoli. ".Constants::ADMIN_CONTACT;

    //Comment
    const COMMENTCREATION_ERROR = "Errore durante l'inserimento del commento. ".Constants::ADMIN_CONTACT;
    const COMMENTDELETE_ERROR = "Errore durante la cancellazione del commento. ".Constants::ADMIN_CONTACT;
    const COMMENTLIST_ERROR = "Impossibile mostrare i commenti. ".Constants::ADMIN_CONTACT;
    const COMMENTUPDATE_ERROR = "Errore durante la modifica del commento. ".Constants::ADMIN_CONTACT;
    const INSERTCOMMENT_ERROR = "Inserisci un commento per continuare";
    const COMMENTLIST_EMPTY = "Questo articolo non contiene alcun commento";

    //Contact section
    const CONTACT_ERROR = "C'è stato un'errore durante l'invio del messaggio. ".Constants::ADMIN_CONTACT;
    const CONTACT_OK = "Il messaggio è stato inviato. Sarai ricontattato il prima possibile.";

    //Cookie
    const COOKIE_ID = 'id';
    const COOKIE_NAME = 'username';
    const COOKIE_PREFERENCE_TIME = 3600;

    //Email
    const EMAIL_ACCOUNT_CREATED = 'Account creato con successo. Per completare la registrazione accedi alla tua casella di posta';
    const EMAIL_ACTIVATION_SUBJECT = 'Attivazione account';
    const EMAIL_ERROR = "Errore durante l'invio della mail. ".Constants::ADMIN_CONTACT;

    //Generic errors
    const ERROR_UNKNOWN = 'Errore sconosciuto';
    const ERROR_CONFIRM_PASSWORD_DIFFERENT = 'Le due password non coincidono';
    const ERROR_TOKEN_MISSED = 'Fornisci un token di autorizzazione per continuare';
    const FILL_ALL_FIELDS = 'Inserisci tutti i dati richiesti per continuare';

    //File
    const FILE_LOG = "./log.txt";

    //History
    const HISTORYITEM_DELETE_ERROR = "Errore durante la rimozione dell'azione. ".Constants::ADMIN_CONTACT;
    const HISTORYITEM_DELETE_OK = "L'azione è stata rimossa con successo";

    //Keys
    const KEY_AUTH = "AngularBlogAuth";
    const KEY_DATA = "data";
    const KEY_DONE = "done";
    const KEY_EMPTY = "empty";
    const KEY_EXPIRED = "expired";
    const KEY_MESSAGE = "msg";

    //Login
    const LOGIN_ERROR = "Errore durante il login. ".Constants::ADMIN_CONTACT;
    const LOGIN_NOTLOGGED = "Devi effettuare l'accesso per eseguire questa operazione";
    const LOGOUT_ERROR = "Errore durante il logout. ".Constants::ADMIN_CONTACT;
    const LOGOUT_ERROR_USERNOTFOUND = "Impossibile completare il logout perché la chiave passata non è valida";

    //Name and surname
    const NAMES_UPDATE_OK = "Il nome e il cognome sono stati aggiornati";
    const NAMES_UPDATE_ERROR = "Errore durante la modifica del nome e del cognome. ".Constants::ADMIN_CONTACT;

    //News
    const NEWS_ERROR = "Errore durante la lettura degli ultimi articoli. ".Constants::ADMIN_CONTACT;
    const NEWS_EMPTY = "Nessun articolo da mostrare";

    //Password
    const PASSWORD_UPDATE_OK = "La password è stata aggiornata";
    const PASSWORD_UPDATE_ERROR = "Errore durante la modifica della password. ".Constants::ADMIN_CONTACT;

    //Registration
    const REG_ERROR = "Errore durante la registrazione dell' account. ".Constants::ADMIN_CONTACT;
    //const REG_SUBSCRIBE_LINK = Cf::ANGULAR_MAIN_URL.'/attiva';

    //Search
    const SEARCH_ERROR = "Errore durante la ricerca degli articoli. ".Constants::ADMIN_CONTACT;

    //Username update
    const USERNAME_UPDATE_OK = "Il nome utente è stato aggiornato";
    const USERNAME_UPDATE_ERROR = "Errore durante la modifica del nome utente. ".Constants::ADMIN_CONTACT;

    //Other
    const ADMIN_CONTACT = "Se il problema persiste contattare l' amministratore del sito";
}
?>