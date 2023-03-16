<?php

namespace AngularBlog\Interfaces\Action;

interface ActionErrors{
    //number
    const INVALIDDATAFORMAT = 21;

    //messages
    const INVALIDDATAFORMAT_MSG = "I dati sono in un formato non valido";

    //other
    const ACTION_RANGE_MIN = 21; //minimum error code of Action class
    const ACTION_RANGE_MAX = 50; //maximum error code of Action class
}
?>