<?php

namespace AngularBlog\Interfaces\Article\Comment;

interface DeleteControllerErrors{
    //Exceptions
    const NOCOMMENTINSTANCE_EXC = "L'oggetto Comment è uguale a null";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token è uguale a null";
    const INVALIDCOMMENTTYPE_EXC = "Il commento fornito non è in un formato valido";
    const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido";
}

?>