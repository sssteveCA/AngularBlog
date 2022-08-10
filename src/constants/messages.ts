export namespace Messages{
    export const ADMIN_CONTACT: string = "Se il problema persiste, contattare l'amministratore";
    export const articleAuthorizedError: string = "C'è stato un errore durante la verifica dell'autorizzazione a modificare l'articolo. "+ADMIN_CONTACT;
export const activationCodeMissing: string = "Inserisci un codice di attivazione";
    export const COMMENTLIST_ERROR: string = "Errore durante la ricerca dei commenti";
    export const INSERTCOMMENT_ERROR: string = "Inserisci un commento per continuare";
    export const deleteArticleConfirm: string = "Sei sicuro di voler rimuovere definitivamente questo articolo?";
    export const deleteArticleError: string = "Errore durante la rimozione dell'articolo. "+ADMIN_CONTACT;
    export const editArticleConfirm: string = "Vuoi modificare l'articolo con le informazioni inserite?";
    export const invalidData: string = "I dati inseriti non sono validi, riprova";
    export const logoutConfirm: string = "Sei sicuro di voler uscire dalla sessione corrente?";
    export const passwordMismatch: string = "Le due password non coincidono";

}