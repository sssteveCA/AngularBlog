<?php

namespace AngularBlog\Interfaces;

//Generic constants used by this app
interface Constants{

    //File
    const FILE_LOG = "./log.txt";

    //MongoDB
    const MONGODB_CONNECTION_STRING = "mongodb://localhost:27017/";
    const MONGODB_DATABASE = 'AngularBlog';
    const MONGODB_COLLECTION_USERS = 'users';
}
?>