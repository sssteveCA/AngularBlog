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
    private $dataCambioPwd;
    private $creation_time; //account creation time
    private $last_modified; //last account modified time
    private $action; /*action that the user perform
    1 = login, 2 = registration, 3 = recovery*/
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

    public function __construct()
    {
        $this->h = new Client(C::MONGODB_CONNECTION_STRING);
        $this->database = $this->h->${C::MONGODB_DATABASE}; //Access to the database
        $this->collection = $this->h->${C::MONGODB_DATABASE}->${C::MONGODB_COLLECTION_USERS};
    }

    public function __destruct()
    {
        
    }
}
?>