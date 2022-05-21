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
        $this->id = isset($dati['id'])? $dati['id']:null;
        $this->name = isset($dati['name'])? $dati['name']:null;
        $this->surname = isset($dati['surname'])? $dati['surname']:null;
        $this->email = isset($dati['email'])? $dati['email']:null;
        $this->username = isset($dati['username'])? $dati['username']:null;
        $this->password = isset($dati['password'])? $dati['password']:null;
        $this->passwordHash = password_hash($this->password,PASSWORD_DEFAULT);
        $this->emailVerif=isset($dati['emailVerif'])? $dati['emailVerif']:null;
        $this->changeVerif=isset($dati['changeVerif'])? $dati['changeVerif']:null;
        /*$this->pwdChangeDate=isset($dati['pwdChangeDate'])? $dati['pwdChangeDate']:null;
        $this->$creation_time=isset($dati['$creation_time'])? $dati['$creation_time']:null;
        $this->last_modified=isset($dati['last_modified'])? $dati['last_modified']:null;*/
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

    public function isLogged(){return $this->logged;}
}
?>