export namespace Messages{
    export const ADMIN_CONTACT: string = "Se il problema persiste, contattare l'amministratore";
    export const ARTICLEAUTHORIZED_ERROR: string = "C'è stato un errore durante la verifica dell'autorizzazione a modificare l'articolo. "+ADMIN_CONTACT;
    export const ARTICLENEW_ERROR: string = "Errore durante la creazione dell'articolo. "+ADMIN_CONTACT;
    export const ARTICLEUPDATE_ERROR: string = "Errore durante la modifica dell'articolo. "+ADMIN_CONTACT;
    export const ARTICLESVIEW_ERROR = "Errore durante la ricerca degli articoli. "+ADMIN_CONTACT;
    export const ACTIVATIONCODEMISSING: string = "Inserisci un codice di attivazione";
    export const COMMENTDELETE_ERROR: string = "Errore durante la cancellazione del commento. "+ADMIN_CONTACT;
    export const COMMENTLIST_ERROR: string = "Errore durante la ricerca dei commenti. "+ADMIN_CONTACT;
    export const COMMENTNEW_ERROR: string = "Errore durante l'inserimento del commento. "+ADMIN_CONTACT;
    export const COMMENTUPDATE_ERROR: string = "Errore durante la modifica del commento. "+ADMIN_CONTACT;
    export const INSERTCOMMENT_ERROR: string = "Inserisci un commento per continuare";
    export const DELETEARTICLE_CONFIRM: string = "Sei sicuro di voler rimuovere definitivamente questo articolo?";
    export const DELETEARTICLE_ERROR: string = "Errore durante la rimozione dell'articolo. "+ADMIN_CONTACT;
    export const DELETECOMMENT_CONFIRM: string = "Vuoi rimuovere questo commento?";
    export const EDITARTICLE_CONFIRM: string = "Vuoi modificare l'articolo con le informazioni inserite?";
    export const INVALIDDATA_ERROR: string = "I dati inseriti non sono validi, riprova";
    export const LOGIN_ERROR:string = "Errore durante il login. "+ADMIN_CONTACT;
    export const LOGOUT_CONFIRM: string = "Sei sicuro di voler uscire dalla sessione corrente?";
    export const PASSWORDMISMATCH: string = "Le due password non coincidono";
    export const SUBSCRIBE_ERROR:string = "Errore durante la registrazione. "+ADMIN_CONTACT;

}