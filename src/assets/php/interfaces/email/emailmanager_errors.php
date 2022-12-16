<?php

namespace AngularBlog\Interfaces\Email;

interface EmailManagerErrors{
    const ERR_EMAIL_SEND = 1;

    const ERR_EMAIL_SEND_MSG = "C'è stato un errore durante l'invio della mail a uno o più destinatari";
}
?>