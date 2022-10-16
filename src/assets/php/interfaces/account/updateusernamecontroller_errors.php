<?php

namespace AngularBlog\Interfaces\Account;

use AngularBlog\Interfaces\ExceptionMessages;
use AngularBlog\Interfaces\FromErrors;

interface UpdateUsernameControllerErrors extends ExceptionMessages, FromErrors{
    const UPDATE_USER = 1;
    const UPDATE_TOKEN = 2;

    const UPDATE_USER_MSG = "";
    const UPDATE_TOKEN_MSG = "";
}

?>