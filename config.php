<?php

namespace AngularBlog;

//Edit this values depending on your environment
interface Config{
    //Angular server
    const ANGULAR_SCHEME = "http";
    const ANGULAR_HOSTNAME = 'localhost';
    const ANGULAR_PORT = 4200;
    const ANGULAR_MAIN_URL = Config::ANGULAR_SCHEME."://".Config::ANGULAR_HOSTNAME.":".Config::ANGULAR_PORT;

    //MongoDB
    const MONGODB_CONNECTION_STRING = "mongodb://localhost:27017";
    const MONGODB_DATABASE = 'AngularBlog';
    const MONGODB_COLLECTION_ARTICLES = 'articles';
    const MONGODB_COLLECTION_COMMENTS = 'comments';
    const MONGODB_COLLECTION_TOKENS = 'tokens';
    const MONGODB_COLLECTION_USERS = 'users';
}