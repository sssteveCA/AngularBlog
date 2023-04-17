<?php

namespace AngularBlog\Interfaces;

interface ExceptionMessages{
    const ARTICLETYPEMISMATCH_EXC = "La variabile Article non è del tipo atteso";
    const COMMENTTYPEMISMATCH_EXC = "La variabile Comment non è del tipo atteso";
    const INVALIDACTIONTYPE_EXC = "L'azione fornita non è in un formato valido";
    const INVALIDARTICLETYPE_EXC = "L'articolo fornito non è in un formato valido";
    const INVALIDCOMMENTTYPE_EXC = "Il commento fornito non è in un formato valido";
    const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido";
    const MISSINGVALUES_EXC = 'Uno o più valori richiesti non sono presenti';
    const NOACTIONINSTANCE_EXC = "L'oggetto Action passato è uguale a null";
    const NOARTICLEINSTANCE_EXC = "L'oggetto Article passato è uguale a null";
    const NOARTICLEDATA_EXC = "Non hai passato i dati dell'articolo da creare";
    const NOARTICLEPERMALINK_EXC = "Non hai passato il permalink dell'articolo";
    const NOCOMMENT_EXC = "Non hai fornito il testo del commento";
    const NOCOMMENTINSTANCE_EXC = "L'oggetto Comment passato è uguale a null";
    const NOTOKENKEY_EXC = "Non è stata fornita la chiave di login";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token passato è uguale a null";
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";
    const TOKENTYPEMISMATCH_EXC = "La variabile Token non è del tipo atteso";
    const USERTYPEMISMATCH_EXC = "La variabile User non è del tipo atteso"; 
}
?>