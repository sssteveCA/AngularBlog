<?php

namespace AngularBlog\Interfaces;

//error constants from User class
interface UserErrors{
    //number
    const INVALIDDATAFORMAT = 21;

    //messages
    const INVALIDDATAFORMAT_MSG = "I dati sono in un formato non valido";

    //other
    const RANGE_MIN = 21; //minimum error code of User class
    const RANGE_MAX = 50; //maximum error code of User class
}
?>