<?php

namespace AngularBlog\Interfaces;

/**
 * Error from external classes codes and messages
 */
interface FromErrors{
    //Numbers
    const FROM_ARTICLE = 1;
    const FROM_TOKEN = 2;
    const FROM_COMMENT = 3;

    //Messages
    const FROM_ARTICLE_MSG = "Errore dalla classe Article";
    const FROM_TOKEN_MSG = "Errore nella classe Token";
    const FROM_COMMENT_MSG = "Errore nella classe Comment";
}
?>