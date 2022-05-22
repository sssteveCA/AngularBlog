<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\BlogUserErrors as Bue;
use AngularBlog\Interfaces\Constants as C;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

//This class interacts with DB for users operations
class BlogUser implements Bue,C{
    private ?Client $h = null; //MongoDB connection handle 
    private bool $connect = false; //true if there is a MongoDB connection
    private ?Database $database ; //MongoDB database used by this class
    private ?Collection $collection; //MongoDB collection of registered users
    private $id;
    private $name;
    private $surname;
    private $username; 
    private $email;
    private $password; 
    private $passwordHash; //password created with hash algorithm
    private $emailVerif; //verification code to complete the registration
    private $changeVerif; //code for request new password
    private $pwdChangeDate;
    private $creation_time; //account creation time
    private $last_modified; //last account modified time
    private $action; /*action that the user perform
    1 = login, 2 = registration, 3 = recovery*/
    private bool $logged = false; //Check if user is logged
    private $headers; //Email headers
    private $message; //Email message
    private int $errno = 0; //error code
    private ?string $error = null;
    public static array $fields = array('id','email','username','emailVerif','changeVerif');
    public static array $regex = array(
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

    public function __construct(array $data)
    {
        $this->h = new Client(C::MONGODB_CONNECTION_STRING);
        $this->database = $this->h->${C::MONGODB_DATABASE}; //Access to the database
        $this->collection = $this->h->${C::MONGODB_DATABASE}->${C::MONGODB_COLLECTION_USERS};
        $this->id = isset($data['id'])? $data['id']:null;
        $this->name = isset($data['name'])? $data['name']:null;
        $this->surname = isset($data['surname'])? $data['surname']:null;
        $this->email = isset($data['email'])? $data['email']:null;
        $this->username = isset($data['username'])? $data['username']:null;
        $this->password = isset($data['password'])? $data['password']:null;
        $this->passwordHash = password_hash($this->password,PASSWORD_DEFAULT);
        $this->emailVerif=isset($data['emailVerif'])? $data['emailVerif']:null;
        $this->changeVerif=isset($data['changeVerif'])? $data['changeVerif']:null;
        /*$this->pwdChangeDate=isset($data['pwdChangeDate'])? $data['pwdChangeDate']:null;
        $this->$creation_time=isset($data['$creation_time'])? $data['$creation_time']:null;
        $this->last_modified=isset($data['last_modified'])? $data['last_modified']:null;*/
    }

    public function __destruct()
    {
        
    }

    //getters
    public function getId(){return $this->id;}
    public function getName(){return $this->name;}
    public function getSurname(){return $this->surname;}
    public function getEmail(){return $this->email;}
    public function getUsername(){return $this->username;}
    public function getPasswordHash(){return $this->passwordHash;}
    public function getEmailVerif(){return $this->emailVerif;}
    public function getChangeVerif(){return $this->changeVerif;}
    public function getPwdChangeDate(){return $this->pwdChangeDate;}
    public function getCrTime(){return $this->creation_time;}
    public function getLastMod(){return $this->last_modified;}
    public function getAction(){return $this->action;}
    public function getQuery(){return $this->query;}
    public function getQueries(){return $this->queries;}
    public function getTable(){return $this->table;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            case Bue::INVALIDDATAFORMAT:
                $this->error = Bue::INVALIDDATAFORMAT_MSG;
                break;
            case Bue::USERNAMEMAILEXIST:
                $this->error = Bue::USERNAMEMAILEXIST_MSG;
                break;
            case Bue::MAILNOTSENT:
                $this->error = Bue::MAILNOTSENT_MSG;
                break;
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }

    public function isLogged(){return $this->logged;}

    //create the account activation or password recovery code 
    public function codAutGen($order): string{
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
        if($order == '0') return $codAut.$s;
        else return $s.$codAut;
    }

    /*check if field has particular value
    1 = the field already has that value
    0 = the field has not that value
    -1 = error */
    private function exists($filter): int{
        $this->errno = 0;
        $ret = 0;
        $document = $this->collection->findOne($filter);
        if($document != null)$ret = 1; //Document found with this filter
        return $ret;
    }

    //insert class properties in database
    private function insert(): bool{
        $insert = false;
        $this->errno = 0;
        $this->emailVerif = $this->codAutGen('0');
        $this->creation_time = date('Y-m-d H:i:s');
        $this->last_modified = date('Y-m-d H:i:s');
        $values = array(
            'name' => $this->name,
            'surname' => $this->surname,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->passwordHash,
            'creation_time' => $this->creation_time,
            'last_modified' => $this->last_modified
        );
        $insertOne = $this->collection->insertOne($values);
        $insert = true;
        return $insert;
    }

    //store account registration values in DB
    public function registration() : bool{
        $this->errno = 0;
        $registration = false;
        $verify = $this->validate();
        if($verify){
            //values are all valid
            $filter = array(
                '$or' => [
                    ['username' => $this->username],
                    ['email' => $this->email]
                ]
            );
            $exists = $this->exists($filter);
            if($exists == 0){
                //Account with username or email guven not extst, values can be inserted
                $insert = $this->insert();
                if($insert){
                    $send = $this->sendEmail();
                    if($send){
                        $registration = true;
                    }
                }//if($insert){        
            }//if($exists == 0){
            else
                $this->errno = Bue::USERNAMEMAILEXIST;

        }//if($verify){
        else
            $this->errno = Bue::INVALIDDATAFORMAT;
        return $registration; 
    }

    //user send an email
    public function sendEmail(): bool{
        $this->errno = 0;
        $this->setHeaders();
        $this->setMessage();
        $send = @mail($this->email,C::EMAIL_ACTIVATION_SUBJECT,$this->message,$this->headers);
        if(!$send) $this->errno = Bue::MAILNOTSENT; //email non inviata
        return $send;
    }

    //Set the email headers
    private function setHeaders(){
        //mail headers
$this->headers = <<<HEADER
From: Admin <noreply@localhost.lan>
Reply-to: <noreply@localhost.lan>
Content-type: text/html
MIME-Version: 1.0
HEADER;
    }

    //Set email message
    private function setMessage(){
        $indAtt = C::REG_SUBSCRIBE_LINK;
        $codIndAtt = $indAtt.'?emailVerif='.$this->emailVerif;
        $this->message = <<<HTML
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
<p>{$this->emailVerif}</p>
        </div>
    </body>
</html>
HTML;
    }


     //check if properties are all valid before insert
     private function validate(){
        $valid = true;
        if(isset($this->id) && !preg_match(BlogUser::$regex['id'],$this->id)){
            file_put_contents("log.txt","BlogUser validate() id ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->name) && !preg_match(BlogUser::$regex['name'],$this->name)){
            file_put_contents("log.txt","BlogUser validate() name ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->surname) && !preg_match(BlogUser::$regex['surname'],$this->surname)){
            file_put_contents("log.txt","BlogUser validate() surname ",FILE_APPEND);
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
        if(isset($this->pwdChangeDate) && !preg_match(BlogUser::$regex['pwdChangeDate'],$this->pwdChangeDate)){
            file_put_contents("log.txt","BlogUser validate() pwdChangeDate ",FILE_APPEND);
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