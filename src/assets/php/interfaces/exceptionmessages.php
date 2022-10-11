<?php

namespace AngularBlog\Interfaces;

interface ExceptionMessages{
    const NOTOKENINSTANCE_EXC = "L'oggetto Token passato è uguale a null";
    const NOUSERINSTANCE_EXC = "L'oggetto User passato è uguale a null";
    const USERTYPEMISMATCH_EXC = "La variabile User non è del tipo atteso";
    const TOKENTYPEMISMATCH_EXC = "La variabile Token non è del tipo atteso";
}
?>