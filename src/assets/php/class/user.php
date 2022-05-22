<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Model;

//This class interacts with MongoDB database for the User collection
class User extends Model implements C{
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

    private int $errno = 0; //error code
    private ?string $error = null;
    private static string $logFile = C::FILE_LOG;

    public static array $regex = array(
        'id' => '/^[0-9]+$/',
        'name' => '/^[a-z]{3,}$/i',
        'surname' => '/^[a-z]{2,}$/i',
        'email' => '/^[a-zA-Z-_0-9]{4,20}@([a-z]{3,15}\.){1,6}[a-z]{2,10}$/',
        'username' => '/^.+$/i',
        'password' => '/^.{6,}$/i',
        'emailVerif' => '/^[a-z0-9]{64}$/i',
        'changeVerif' => '/^[a-z0-9]{64}$/i',
        'time' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'
    );

    public function __construct(array $data)
    {
        $this->connection_url = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $this->database_name = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $this->collection_name = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_USERS;
        parent::__constructor($data);
        $this->id = isset($data['id'])? $data['id']:null;
        $this->name = isset($data['name'])? $data['name']:null;
        $this->surname = isset($data['surname'])? $data['surname']:null;
        $this->email = isset($data['email'])? $data['email']:null;
        $this->username = isset($data['username'])? $data['username']:null;
        $this->password = isset($data['password'])? $data['password']:null;
        $this->passwordHash = password_hash($this->password,PASSWORD_DEFAULT);
        $this->emailVerif=isset($data['emailVerif'])? $data['emailVerif']:null;
        $this->changeVerif=isset($data['changeVerif'])? $data['changeVerif']:null;
        $this->subscribed=isset($data['subscribed'])? $data['subscribed']: false;
        /*$this->pwdChangeDate=isset($data['pwdChangeDate'])? $data['pwdChangeDate']:null;
        $this->$creation_time=isset($data['$creation_time'])? $data['$creation_time']:null;
        $this->last_modified=isset($data['last_modified'])? $data['last_modified']:null;*/

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
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->errno){
            default:
                $this->error = null;
                break;
        }
        return $this->error;
    }
}
?>