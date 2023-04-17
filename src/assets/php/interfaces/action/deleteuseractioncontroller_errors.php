<?php

namespace AngularBlog\Interfaces\Action;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface DeleteUserActionControllerErrors extends ExceptionMessages, FromErrors{
    //Numbers
    const ACTIONNOTDELETED = 2; //Action not deleted from Database

    //Messages
    const ACTIONNOTDELETED_MSG = "L'azione non è stato rimosso";
}
?>