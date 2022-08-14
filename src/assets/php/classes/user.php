<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Interfaces\ModelErrors as Me;
use AngularBlog\Interfaces\UserErrors as Ue;
use AngularBlog\Classes\Model;

//This class interacts with MongoDB database for the User collection
class User extends Model implements Ue{
    private ?string $id;
    private ?string $name;
    private ?string $surname;
    private ?string $username; 
    private ?string $email;
    private ?string $password; 
    private ?string $passwordHash; //password created with hash algorithm
    private ?string $emailVerif; //verification code to complete the registration
    private ?string $changeVerif; //code for request new password
    private ?string $pwdChangeDate;
    private ?string $creation_time; //account creation time
    private ?string $last_modified; //last account modified time
    private bool $subscribed; //true if user is alterady susbscribed to blog

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
        /*'time' => '/^([0-9]+)\s(2[0-3]|1[0-9]|[0-9])\s([0-5][0-9]|[0-9])\s([0-5][0-9]|[0-9])$/'*/
        'time' => '/^[0-9]{4}-(0[1-9]|1[0-2])-([012][0-9]|3[01])\s+([0-1][0-9]|2[0-3])(:[0-5][0-9]){2}$/i'
    );

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_USERS;
        parent::__construct($data);
        $indexArr = [
            ['key' => ['username' => 1], 'unique' => true],
            ['key' => ['email' => 1], 'unique' => true]
        ];
        $this->collection->createIndexes($indexArr);
        $this->id = isset($data['id'])? $data['id']:null;
        $this->name = isset($data['name'])? $data['name']:null;
        $this->surname = isset($data['surname'])? $data['surname']:null;
        $this->email = isset($data['email'])? $data['email']:null;
        $this->username = isset($data['username'])? $data['username']:null;
        $this->password = isset($data['password'])? $data['password']:null;
        $this->passwordHash = isset($this->password) ? password_hash($this->password,PASSWORD_DEFAULT): null;
        $this->emailVerif=isset($data['emailVerif'])? $data['emailVerif']:null;
        $this->changeVerif=isset($data['changeVerif'])? $data['changeVerif']:null;
        $this->subscribed=isset($data['subscribed'])? $data['subscribed']: false;
        $this->pwdChangeDate=isset($data['pwdChangeDate'])? $data['pwdChangeDate']:null;
        $this->creation_time=isset($data['$creation_time'])? $data['$creation_time']:null;
        $this->last_modified=isset($data['last_modified'])? $data['last_modified']:null;

    }

    //getters
    public function getId(){return $this->id;}
    public function getName(){return $this->name;}
    public function getSurname(){return $this->surname;}
    public function getEmail(){return $this->email;}
    public function getUsername(){return $this->username;}
    public function getPassword(){return $this->password;}
    public function getPasswordHash(){return $this->passwordHash;}
    public function getEmailVerif(){return $this->emailVerif;}
    public function getChangeVerif(){return $this->changeVerif;}
    public function getPwdChangeDate(){return $this->pwdChangeDate;}
    public function getCrTime(){return $this->creation_time;}
    public function getLastMod(){return $this->last_modified;}
    public function getError(){
        if($this->errno <= Me::MODEL_RANGE_MAX){
            //An error of superclass
            return parent::getError();
        }
        else{
            switch($this->errno){
                case Ue::INVALIDDATAFORMAT:
                    $this->error = Ue::INVALIDDATAFORMAT_MSG;
                    break;
				default:
                    $this->error = null;
                    break;
            }
        }
        return $this->error;
    }

    public function isSubscribed(){return $this->subscribed;}

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

    public function user_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $this->emailVerif = $this->codAutGen('0');
        $this->creation_time = date('Y-m-d H:i:s');
        $this->last_modified = date('Y-m-d H:i:s');
        $this->subscribed = false;
        if($this->validate()){
            //All data are valid and can be inserted
            $values = array(
                'name' => $this->name,
                'surname' => $this->surname,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->passwordHash,
                'changeVerif' => $this->changeVerif,
                'emailVerif' => $this->emailVerif,
                'creation_time' => $this->creation_time,
                'last_modified' => $this->last_modified,
                'subscribed' => $this->subscribed
            );
            parent::create($values);
            if($this->errno == 0)$inserted = true;
        }//if($this->validate()){
        else
            $this->errno = Ue::INVALIDDATAFORMAT;
        return $inserted;
    }

    public function user_delete(array $filter): bool{
        $deleted = false;
        $this->errno = 0;
        parent::delete($filter);
        if($this->errno == 0)$deleted = true;
        return $deleted;
    }

    public function user_get(array $filter): bool{
        $got = false;
        $this->errno = 0;
        $user = parent::get($filter);
        if($this->errno == 0){
            $this->id = $user["_id"];
            $this->name = $user["name"];
            $this->surname = $user["surname"];
            $this->username = $user["username"];
            $this->email = $user["email"];
            $this->passwordHash = $user["password"];
            $this->emailVerif = $user["emailVerif"];
            $this->changeVerif = $user["changeVerif"];
            $this->creation_time = $user["creation_time"];
            $this->last_modified = $user["last_modified"];
            $this->subscribed = $user["subscribed"];
            $got = true;
        }//if($this->errno == 0){
        return $got;
    }

    public function user_update(array $filter, array $data): bool{
        $updated = false;
        $this->errno = 0;
		$data['$set']['last_modified'] = date('Y-m-d H:i:s');
        parent::update($filter,$data);
        if($this->errno == 0)$updated = true;
        return $updated;
    }
    
    //check if properties are all valid before insert
    private function validate(){
        $valid = true;
        if(isset($this->id) && !preg_match(User::$regex['id'],$this->id)){
            file_put_contents(C::FILE_LOG,"User validate() id ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->name) && !preg_match(User::$regex['name'],$this->name)){
            file_put_contents(C::FILE_LOG,"User validate() name ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->surname) && !preg_match(User::$regex['surname'],$this->surname)){
            file_put_contents(C::FILE_LOG,"User validate() surname ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->username) && !preg_match(User::$regex['username'],$this->username)){
            file_put_contents(C::FILE_LOG,"User validate() username ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->email) && !preg_match(User::$regex['email'],$this->email)){
            file_put_contents(C::FILE_LOG,"User validate() email {$this->email} ",FILE_APPEND);
            $valid = false;
        }
        /*if(isset($this->password) && !preg_match(User::$regex['password'],$this->password)){
            $valid = false;
        }*/
        if(isset($this->emailVerif) && !preg_match(User::$regex['emailVerif'],$this->emailVerif)){
            file_put_contents(C::FILE_LOG,"User validate() emailVerif ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->changeVerif) && !preg_match(User::$regex['changeVerif'],$this->changeVerif)){
            file_put_contents(C::FILE_LOG,"User validate() changeVerif ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->pwdChangeDate) && !preg_match(User::$regex['time'],$this->pwdChangeDate)){
            file_put_contents(C::FILE_LOG,"{$this->pwdChangeDate}",FILE_APPEND);
            file_put_contents(C::FILE_LOG,"User validate() pwdChangeDate ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->creation_time) && !preg_match(User::$regex['time'],$this->creation_time)){
            file_put_contents(C::FILE_LOG,"{$this->creation_time}",FILE_APPEND);
            file_put_contents(C::FILE_LOG,"User validate() cr_time ",FILE_APPEND);
            $valid = false;
        }
        if(isset($this->last_modified) && !preg_match(User::$regex['time'],$this->last_modified)){
            file_put_contents(C::FILE_LOG,"{$this->last_modified}",FILE_APPEND);
            file_put_contents(C::FILE_LOG,"User validate() last_mod ",FILE_APPEND);
            $valid = false;
        }
        return $valid;
    }


}
?>