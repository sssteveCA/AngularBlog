<?php

namespace AngularBlog\Interfaces;

/**
 * Error from external classes codes and messages
 */
interface FromErrors{
    //Numbers
    const FROM_ARTICLE = 50;
    const FROM_TOKEN = 51;
    const FROM_COMMENT = 52;
    const FROM_USER = 53; //Error from User instance
    const FROM_ACTION = 54;
    const FROM_COMMENTAUTHORIZEDCONTROLLER = 60;
    const FROM_ARTICLEAUTHORIZEDCONTROLLER = 61;
    const FROM_USERAUTHORIZEDCONTROLLER = 62;
    const FROM_ACTIONAUTHORIZEDCONTROLLER = 63;

    //Messages
    const FROM_ARTICLE_MSG = "Errore nell' oggetto Article";
    const FROM_TOKEN_MSG = "Errore nell' oggetto Token";
    const FROM_COMMENT_MSG = "Errore nell' oggetto Comment";
    const FROM_USER_MSG = "Errore nell' oggetto User";
    const FROM_ACTION_MSG = "Errore nell' oggetto Action";
    const FROM_COMMENTAUTHORIZEDCONTROLLER_MSG = "Errore nell'oggetto CommentAuthorizedController";
    const FROM_ARTICLEAUTHORIZEDCONTROLLER_MSG = "Errore nell'oggetto ArticleAuthorizedController";
    const FROM_USERAUTHORIZEDCONTROLLER_MSG = "Errore nell'oggetto UserAuthorizedController";
    const FROM_ACTIONAUTHORIZEDCONTROLLER_MSG = "Errore nell'oggetto ActionAuthorizedController";
}
?>