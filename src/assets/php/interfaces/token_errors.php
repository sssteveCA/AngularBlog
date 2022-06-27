<?php

namespace AngularBlog\Interfaces; 

interface TokenErrors{
    //Exceptions

    //Numbers
    const TOKENEXPIRED = 1;

    //Messages
    const TOKENEXPIRED_MSG = "La tua sessione è scaduta. Ripeti il login";
}
?>