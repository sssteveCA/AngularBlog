<?php

//database constants
const HOSTNAME = 'localhost';
const USERNAME = 'root';
const PASSWORD = '';
const DATABASE = 'stefano';
const TABLE_ARTICLES = 'articoli';
const TABLE_USERS = 'utentiBlog';

//error messages
const UNKNOWN_ERROR = 'Errore sconosciuto';

//account activation link
$att = "http://localhost:4200/attiva";

//cookie
const COOKIE_NAME = 'username';

//mail headers
$headers = <<<HEADER
From: Admin <noreply@localhost.lan>
Reply-to: <noreply@localhost.lan>
Content-type: text/html
MIME-Version: 1.0
HEADER;

//mail body
function mailHtml($emailCode,$indAtt,$codIndAtt){
    $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Attivazione account</title>
        <meta charset="utf-8">
        <style>
            body{
                display: flex;
                justify-content: center;
            }
            div#linkAtt{
                padding: 10px;
                background-color: orange;
                font-size: 20px;
            }
        </style>
    <head>
    <body>
        <div id="linkAtt">
Completa la registrazione facendo click sul link sottostante:
<p><a href="{$codIndAtt}">{$codIndAtt}</a></p>
oppure vai all'indirizzo <p><a href="{$indAtt}">{$indAtt}</a></p> e incolla il seguente codice: 
<p>{$emailCode}</p>
        </div>
    </body>
</html>
HTML;
    return $html;
}

//html for email contact
function htmlMailContact($message){
    $html = <<<HTML
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Attivazione account</title>
        <meta charset="utf-8">
        <style>
            div{
                padding: 20px;
                outline: 5px groove black;
                font-size: 20px;
                font-weight: bold;
            }
        </style>
    <head>
    <body>
        <div>{$message}</div>
    </body>
</html>
HTML;
    return $html;
}

//email constants
const ADMINEMAIL = 'admin@localhost.lan';
const EMAILREGEX = '/^[a-zA-Z-_0-9]{4,20}@([a-z]{3,15}\.){1,6}[a-z]{2,10}$/';


?>