<?php

require_once("../config.php");

define("BLOGUSER_INCORRECTLOGINDATA", "1");
define("BLOGUSER_ACTIVEYOURACCOUNT", "2");
define("BLOGUSER_DATANOTUPDATED", "3");
define("BLOGUSER_DATANOTINSERTED", "4");
define("BLOGUSER_QUERYERROR", "5");
define("BLOGUSER_USERNAMEMAILEXIST", "6");
define("BLOGUSER_ACCOUNTNOTACTIVATED", "7");
define("BLOGUSER_MAILNOTSENT", "8");
define("BLOGUSER_INVALIDFIELD", "9");
define("BLOGUSER_DATANOTSET", "10");
define("BLOGUSER_ACCOUNTNOTRECOVERED", "11");
define("BLOGUSER_INVALIDDATAFORMAT", "12");

class BlogUser{
    private $h; //MySql connection handle 
    private $connect; //true if there is a MySql connection
    private $table; //tabella MySql degli utenti registrati
    private $id;
    private $nome;
    private $cognome;
    private $username; 
    private $email;
    private $password;
    private $emailVerif; //verification code to complete the registration
    private $changeVerif; //code for request new password
    private $dataCambioPwd;
    private $cr_time; //account creation time
    private $last_mod; //last account modified time
    private $action; /*action that the user perform
    1 = login, 2 = registration, 3 = recovery*/
    private $logged; //true if user it's logged
    private $query; //ultima query SQL inviata
    private $queries; //lista di query SQL inviate
    private $errno; //codice dell'errore rilevato
    public static $campi = array('id','email','username','emailVerif','changeVerif');
    public static $regex = array(
        'id' => '/^[0-9]+$/',
        'nome' => '/^[a-z]{3,}$/i',
        'cognome' => '/^[a-z]{2,}$/i',
        'email' => '/^[a-zA-Z-_0-9]{4,20}@([a-z]{3,15}\.){1,6}[a-z]{2,10}$/',
        'username' => '/^.+$/i',
        'password' => '/^.{6,}$/i',
        'emailVerif' => '/^[a-z0-9]{64}$/i',
        'changeVerif' => '/^[a-z0-9]{64}$/i',
        'tempo' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'
    );

    public function __construct($dati){
        $this->connesso = false;
        $mysqlHost=isset($dati['mysqlHost'])? $dati['mysqlHost']:HOSTNAME;
        $mysqlUser=isset($dati['mysqlUser'])? $dati['mysqlUser']:USERNAME;
        $mysqlPass=isset($dati['mysqlPass'])? $dati['mysqlPass']:PASSWORD;
        $mysqlDb=isset($dati['mysqlDb'])? $dati['mysqlDb']:DATABASE;
        $this->tabella=isset($dati['tabella'])? $dati['tabella']:TABLE_USERS;
        $this->h = new mysqli($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDb);
        if($this->h->connect_errno !== 0){
            throw new Exception("Connessione a MySql fallita: ".$this->h->connect_error);
        }
        $this->h->set_charset("utf8mb4");
        $this->query = null;
        $this->queries = array();
        if(!$this->createDb($mysqlDb)){
            throw new Exception("Errore durante il controllo del database");
        }
        if(!$this->createTable()){
            throw new Exception("Errore durante il controllo della tabella");
        }
        $this->errno = 0;
        $this->connect = true;
        $this->logged = false;
        $this->nome = isset($dati['nome'])? $dati['nome']:null;
        $this->cognome = isset($dati['cognome'])? $dati['cognome']:null;
        $this->email = isset($dati['email'])? $dati['email']:null;
        $this->username = isset($dati['username'])? $dati['username']:null;
        $this->password = isset($dati['password'])? password_hash($dati['password'],PASSWORD_DEFAULT):null;
        $this->emailVerif=isset($dati['emailVerif'])? $dati['emailVerif']:null;
        $this->changeVerif=isset($dati['changeVerif'])? $dati['changeVerif']:null;
        /*$this->dataCambioPwd=isset($dati['dataCambioPwd'])? $dati['dataCambioPwd']:null;
        $this->cr_time=isset($dati['cr_time'])? $dati['cr_time']:null;
        $this->last_mod=isset($dati['last_mod'])? $dati['last_mod']:null;*/

    }//public function __construct($dati){

    public function __destruct(){
        if($this->connect)$this->h->close();
    }

    //getters
    public function getId(){return $this->id;}
    public function getNome(){return $this->nome;}
    public function getCognome(){return $this->cognome;}
    public function getEmail(){return $this->email;}
    public function getUsername(){return $this->username;}
    public function getPassword(){return $this->password;}
    public function getEmailVerif(){return $this->emailVerif;}
    public function getChangeVerif(){return $this->changeVerif;}
    public function getDataCambioPwd(){return $this->dataCambioPwd;}
    public function getCrTime(){return $this->cr_time;}
    public function getLastMod(){return $this->last_mod;}
    public function getAction(){return $this->action;}
    public function getQuery(){return $this->query;}
    public function getQueries(){return $this->queries;}
    public function getTabella(){return $this->tabella;}
    public function getErrno(){return $this->errno;}

    public function isConnected(){return $this->connect;}
    public function isLogged(){return $this->logged;}

     //create database if not exists
     private function createDb($db){
        $ok = false;
        $this->query = <<<SQL
CREATE DATABASE IF NOT EXISTS {$db};
SQL;
        $this->queries[] = $this->query;
        $create = $this->h->query($this->query);
        if($create !== false)
            $ok = true;
        return $ok;
    }

    //create table if not exists
    private function createTable(){
        $ok = false;
        $this->query = <<<SQL
SHOW TABLES LIKE '{$this->table}';
SQL;
        $this->queries[] = $this->query;
        $show = $this->h->query($this->query);
        if($show !== false){
            if($show->num_rows == 0){
                $this->query = <<<SQL
CREATE TABLE `{$this->table}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(25) NOT NULL,
  `cognome` varchar(40) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `emailVerif` varchar(255) DEFAULT NULL COMMENT 'email verification code',
  `changeVerif` int(11) DEFAULT NULL COMMENT 'Code for allow user request new password',
  `creation_time` datetime NOT NULL COMMENT 'Account creation time',
  `last_modified` datetime NOT NULL COMMENT 'Last time value has been edited',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `emailVerif` (`emailVerif`),
  UNIQUE KEY `changeVerif` (`changeVerif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;
            $this->queries[] = $this->query;
            $create = $this->h->query($this->query);
            if($create !== false)
                $ok = true;
            }//if($show->num_rows == 0){
            else
                $ok = true;
        }//if($show !== false){        
        return $ok;
    }//private function createTable(){

     //create the account activation or password recovery code 
     public function codAutGen($ordine){
        $codAut = str_replace('.','a',microtime());
        $codAut = str_replace(' ','b',$codAut);
        $lCod = strlen($codAut);
        $lCas = 64 - $lCod;
        $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYzabcdefghijklmnopqrstuvwxyz0123456789';
        $lc = strlen($c) - 1;
        $s = '';
        for($i = 0; $i < $lCas; $i++)
        {
            $j = mt_rand(0,$lc);
            $s .= $c[$j];
        }
        if($ordine == '0') return $codAut.$s;
        else return $s.$codAut;
    }

    public function registration(){

    }

        
}
?>