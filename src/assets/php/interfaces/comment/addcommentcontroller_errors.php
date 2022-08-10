<?php

namespace AngularBlog\Interfaces\Article\Comment;

interface AddCommentControllerErrors{
    //Exceptions
    const NOARTICLEINSTANCE_EXC = "L'oggetto Article è uguale a null";
    const NOCOMMENTINSTANCE_EXC = "L'oggetto Comment è uguale a null";
    const NOTOKENINSTANCE_EXC = "L'oggetto Token è uguale a null";
    const INVALIDARTICLETYPE_EXC = "L'articolo fornito non è in un formato valido";
    const INVALIDCOMMENTTYPE_EXC = "Il commento fornito non è in un formato valido";
    const INVALIDTOKENTYPE_EXC = "Il token fornito non è in un formato valido";
}

?>