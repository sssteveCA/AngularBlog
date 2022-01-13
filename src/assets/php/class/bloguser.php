<?php

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
define("BLOGUSER_STATEMENTERROR", "13");
define("BLOGUSER_INVALIDINDEX", "14");
define("BLOGUSER_NORESULT", "15");

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
    private $creation_time; //account creation time
    private $last_modified; //last account modified time
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
        $this->connect = false;
        $mysqlHost=isset($dati['mysqlHost'])? $dati['mysqlHost']:HOSTNAME;
        $mysqlUser=isset($dati['mysqlUser'])? $dati['mysqlUser']:USERNAME;
        $mysqlPass=isset($dati['mysqlPass'])? $dati['mysqlPass']:PASSWORD;
        $mysqlDb=isset($dati['mysqlDb'])? $dati['mysqlDb']:DATABASE;
        $this->table=isset($dati['tabella'])? $dati['tabella']:TABLE_USERS;
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
        $this->$creation_time=isset($dati['$creation_time'])? $dati['$creation_time']:null;
        $this->last_modified=isset($dati['last_modified'])? $dati['last_modified']:null;*/

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
    public function getCrTime(){return $this->creation_time;}
    public function getLastMod(){return $this->last_modified;}
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

    /*check if field has particular value
    1 = the field already has that value
    0 = the field has not that value
    -1 = error */
    private function exists($where){
        $this->errno = 0;
        $query = <<<SQL
SELECT * FROM `{$this->table}` WHERE {$where};
SQL;
        $this->query = $query;
        $this->queries[] = $this->query;
        $r = $this->h->query($this->query);
        if($r){
            if($r->num_rows > 0){
                $ret = 1; 
            }
            else $ret = 0;
        }
        else{
            $ret = -1;
            $this->errno = BLOGUSER_QUERYERROR; 
        }
        return $ret;
    }//public function Exists

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

    //retrieve table row specifing an index
    private function getData($index){
        $get = false;
        $this->errno = 0;
        if(in_array($index,BlogUser::$campi)){
            $this->query = <<<SQL
SELECT * FROM `{$this->table}` WHERE `{$index}` = '{$this->{$index}}';
SQL;
            $this->queries[] = $this->query;
            $res = $this->h->query($this->query);
            if($res !== false){
                if($res->num_rows == 1){
                    $row = $res->fetch_assoc();
                    $this->id = $row["id"];
                    $this->nome = $row["nome"];
                    $this->nome = $row["nome"];
                    $this->cognome = $row["cognome"];
                    $this->username = $row["username"];
                    $this->email = $row["email"];
                    $this->password = $row["password"];
                    $this->emailVerif = $row["emailVerif"];
                    $this->changeVerif = $row["changeVerif"];
                    $this->creation_time = $row["creation_time"];
                    $this->last_modified = $row["last_modified"];
                    $get = true;
                }
                else{
                    $this->errno = BLOGUSER_NORESULT;
                }
                $res->free_result();
            }//if($res !== false){
            else{
                $this->errno = BLOGUSER_QUERYERROR;
            }
        }//if(in_array($index,BlogUser::$campi)){
        else{
            $this->errno = BLOGUSER_INVALIDINDEX;
        }
        return $get;
    }

    //insert class properties in database
    private function insert(){
        $insert = false;
        $this->errno = 0;
        $this->emailVerif = $this->codAutGen('0');
        $this->creation_time = date('Y-m-d H:i:s');
        $this->last_modified = date('Y-m-d H:i:s');
        $this->query = <<<SQL
INSERT INTO `{$this->table}` (`nome`,`cognome`,`username`,`email`,`password`,`emailVerif`,`creation_time`,`last_modified`)
VALUES (?,?,?,?,?,?,?,?);
SQL;
        $this->queries[] = $this->query;
        $stat = $this->h->prepare($this->query);
        if($stat !== false){
            $stat->bind_param("ssssssss",$this->nome,$this->cognome,$this->username,$this->email,$this->password,$this->emailVerif,$this->creation_time,$this->last_mod);
            $exec = $stat->execute();
            if($exec !== false){
                //successufly inserted data in DB
                $insert = true;
            }//if($result !== false){
            else{
                $this->errno = BLOGUSER_DATANOTINSERTED;
            }
        }//if($stat !== false){
        else{
            $this->errno = BLOGUSER_STATEMENTERROR;
        }
        return $insert;
    }

    //set email verification code
    private function setEmailVerif($emailVerif){$this->emailVerif = $emailVerif;}

    //set LastModified value specifing also the index
    private function setLastModified($lastModified,$index){
        $set = false;
        $this->errno = 0;
        if(in_array($index,BlogUser::$campi)){
            $this->query = <<<SQL
SQL;
        }//if(in_array($index,BlogUser::$campi)){
        else{
            $this->errno = BLOGUSER_INVALIDINDEX;
        }
        return $set;
    }

    //complete registration and activate account
    public function attiva(){
        $ok = false;
        $this->errno = 0;
        if(isset($this->emailVerif)){
            $this->query = <<<SQL
UPDATE `{$this->table}` SET `emailVerif` = NULL WHERE `emailVerif` = '{$this->emailVerif}';
SQL;
            $this->queries[] = $this->query;
            $update = $this->h->query($this->query);
            if($update !== false){
                if($this->h->affected_rows == 1){
                    $ok = true;
                }
                else $this->errno = BLOGUSER_ACCOUNTNOTACTIVATED;
            }
            else $this->errno = BLOGUSER_QUERYERROR;
        }//if(isset($this->emailVerif)){
        else{
            $this->errno = BLOGUSER_DATANOTSET;
        }
        return $ok;
    }

    //store account registration values in DB
    public function registration(){
        $this->errno = 0;
        $registration = false;
        $verify = $this->validate();
        //check for duplicates on UNIQUE keys
        if($verify){
            //values are all valid
            $where = <<<SQL
`username` = '{$this->username}' OR `email` = '{$this->email}';
SQL;
            $exists = $this->exists($where);
            if($exists == 0){
                //the values compared were not found in all UNIQUE index
                $insert = $this->insert();
                if($insert)
                    $registration = true;
            }
            else if($exists == 1){
                $this->errno = BLOGUSER_USERNAMEMAILEXIST;
            }
        }//if($verify){
        else{
            $this->errno = BLOGUSER_INVALIDDATAFORMAT;
        }
        return $registration;
    }

    //user send an email
    public function sendEmail($to,$subject,$body,$headers){
        $this->errno = 0;
        $from = $this->getEmail();
        $send = @mail($to,$subject,$body,$headers);
        if(!$send) $this->errno = BLOGUSER_MAILNOTSENT; //email non inviata
        return $send;
    }

    public function update($index){
        $ok = false;
        $this->errno = 0;
        if(in_array($index,BlogUser::$campi)){
            $u = 1; //count field to be updated
            $sql = "UPDATE `{$this->table}` ";
            if(isset($this->nome)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`nome` = '{$this->nome}'";
            }
            if(isset($this->cognome)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`cognome` = '{$this->cognome}'";
            }
            if(isset($this->username)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`username` = '{$this->username}'";
            }
            if(isset($this->email)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`email` = '{$this->email}'";
            }
            if(isset($this->password)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`password` = '{$this->password}'";
            }
            if(isset($this->emailVerif)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`emailVerif` = '{$this->emailVerif}'";
            }
            else{
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`emailVerif` = NULL";
            }
            if(isset($this->changeVerif)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`changeVerif` = '{$this->changeVerif}'";
            }
            else{
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`changeVerif` = NULL";
            }
            if(isset($this->last_modified)){
                if($u <= 1){$sql .= "SET "; $u++;}
                $sql .= "`last_modified` = '{$this->last_modified}'";
            }
            $sql .= ";";
            $this->query = $sql;
            $this->queries[] = $this->query;
            $update = $this->h->query($this->query);
            if($update !== false){
                if($this->h->affected_rows == 1)
                    $ok = true;
                else 
                    $this->errno = BLOGUSER_DATANOTUPDATED;
            }//if($update !== false){
            else{
                $this->errno = BLOGUSER_QUERYERROR;
            }
        }//if(in_array($index,BlogUser::$campi)){
        else{
            $this->errno = BLOGUSER_INVALIDINDEX;
        }
        return $ok;
    }


    //check if properties are all valid before insert
    private function validate(){
        $valid = true;
        if(isset($this->id) && !preg_match(BlogUser::$regex['id'],$this->id)){
            file_put_contents("log.txt","BlogUser validate() id ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->nome) && !preg_match(BlogUser::$regex['nome'],$this->nome)){
            file_put_contents("log.txt","BlogUser validate() nome ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->cognome) && !preg_match(BlogUser::$regex['cognome'],$this->cognome)){
            file_put_contents("log.txt","BlogUser validate() cognome ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->username) && !preg_match(BlogUser::$regex['username'],$this->username)){
            file_put_contents("log.txt","BlogUser validate() username ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->email) && !preg_match(BlogUser::$regex['email'],$this->email)){
            file_put_contents("log.txt","BlogUser validate() email {$this->email} ",FILE_APPEND);
            $valid = false;
        }
        /*if(isset($this->password) && !preg_match(BlogUser::$regex['password'],$this->password)){
            $valid = false;
        }*/
        if(isset($this->emailVerif) && !preg_match(BlogUser::$regex['emailVerif'],$this->emailVerif)){
            file_put_contents("log.txt","BlogUser validate() emailVerif ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->changeVerif) && !preg_match(BlogUser::$regex['changeVerif'],$this->changeVerif)){
            file_put_contents("log.txt","BlogUser validate() changeVerif ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->dataCambioPwd) && !preg_match(BlogUser::$regex['dataCambioPwd'],$this->dataCambioPwd)){
            file_put_contents("log.txt","BlogUser validate() dataCambioPwd ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->creation_time) && !preg_match(BlogUser::$regex['cr_time'],$this->creation_time)){
            file_put_contents("log.txt","BlogUser validate() cr_time ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->last_modified) && !preg_match(BlogUser::$regex['last_modified'],$this->last_modified)){
            file_put_contents("log.txt","BlogUser validate() last_mod ",FILE_APPEND);
            $valid = false;
        }
        return $valid;
    }

        
}
?>