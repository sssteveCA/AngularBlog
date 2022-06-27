<?php

namespace AngularBlog\Interfaces; 

interface TokenErrors{
    //Exceptions

    //Numbers
    const TOKENEXPIRED = 21;

    //Messages
    const TOKENEXPIRED_MSG = "La tua sessione è scaduta. Ripeti il login";
}
?>