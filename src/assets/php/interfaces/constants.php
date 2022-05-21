<?php

namespace AngularBlog\Interfaces;

//Generic constants used by this app
interface Constants{

    //Email
    const EMAIL_ACTIVATION_SUBJECT = 'Attivazione account';

    //File
    const FILE_LOG = "./log.txt";

    //MongoDB
    const MONGODB_CONNECTION_STRING = "mongodb://localhost:27017/";
    const MONGODB_DATABASE = 'AngularBlog';
    const MONGODB_COLLECTION_USERS = 'users';

    //Registration
    const REG_SUBSCRIBE_LINK = 'http://localhost:4200/attiva';
}
?>