<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Model;
use MongoDB\BSON\ObjectId;
use AngularBlog\Interfaces\ModelErrors as Me;
use AngularBlog\Interfaces\TokenErrors as Te;

//This class is used to store info about logged users

class Token extends Model implements Te{
    private ?string $id;
    private ?string $user_id; //Id of logged user
    private ?string $username; //Username of logged user
    private ?string $token_key; //generated unique key when user log in
    private ?string $logged_time; //Date when specific user has logged
    private bool $expired = false; //True if the token is expired and the user must login again
    private static int $key_length = 80; //Token key string length
    private static int $token_duration = 30; //Token duration in seconds
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_TOKENS;
        parent::__construct($data);
        $indexArr = [
            ['key' => ['user_id' => 1 ], 'unique' => true],
            ['key' => ['username' => 1], 'unique' => true],
            ['key' => ['token_key' => 1], 'unique' => true]
        ];
        $this->collection->createIndexes($indexArr);
        $this->id = isset($data['id'])? $data['id']: null;
        $this->user_id = isset($data['user_id'])? $data['user_id']: null;
        $this->username = isset($data['username'])? $data['username']: null;
        $this->token_key = isset($data['token_key'])? $data['token_key']: null;
        $this->logged_time = isset($data['logged_time'])? $data['logged_time']: null;
    }

    public function getId(){return $this->id;}
    public function getUserId(){return $this->user_id;}
    public function getUsername(){return $this->username;}
    public function getTokenKey(){return $this->token_key;}
    public function getLoggedTime(){return $this->logged_time;}
    public function isExpired(){return $this->expired;}
    public function getError(){
        if($this->errno <= Me::MODEL_RANGE_MAX){
            return parent::getError();
        }
        else{
            switch($this->errno){
                case Te::TOKENEXPIRED:
                    $this->error = Te::TOKENEXPIRED_MSG;
                    break;
                default:
                    $this->error = null;
                    break;
            }
        }
        return $this->error;
    }

    //Generate the unique key
    private function keyGen(){
        $time = str_replace('.','a',microtime());
        $time = str_replace(' ','b',$time);
        $lTime = strlen($time);
        $lGen = Token::$key_length - $lTime;
        $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $lc = strlen($c) - 1;
        $s = "";
        for($i = 0; $i < $lGen; $i++){
            $j = mt_rand(0,$lc);
            $s .= $c[$j];
        }//for($i = 0; $i < $lGen; $i++){
        $this->token_key = $time.$s;
    }

    //Controls if the token is expired
    public function expireControl(){
        $this->expired = false;
        $this->errno = 0;
        if(isset($this->logged_time)){
            //if user logged date exists
            $now = time();
            file_put_contents(Token::$logFile,"Token now => ".var_export($now,true)."\r\n",FILE_APPEND);
            $logged_timestamp = strtotime($this->logged_time);
            file_put_contents(Token::$logFile,"Token logged timestamp => ".var_export($logged_timestamp,true)."\r\n",FILE_APPEND);
            $time_elasped = $now - $logged_timestamp;
            file_put_contents(Token::$logFile,"Token time elasped ts => ".var_export($time_elasped,true)."\r\n",FILE_APPEND);
            file_put_contents(Token::$logFile,"Token token duration => ".var_export(Token::$token_duration,true)."\r\n",FILE_APPEND);
            if($time_elasped > Token::$token_duration){
                //Token is expired
                file_put_contents(Token::$logFile,"Token expired => \r\n",FILE_APPEND);
                $this->errno = Te::TOKENEXPIRED;
                $this->expired = true;
            }
        }//if(isset($this->logged_time)){
        else{
            file_put_contents(Token::$logFile,"Token logged time not set => \r\n",FILE_APPEND);
        }
    }

    //Insert a new Token(when an used sign in)
    public function token_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $this->keyGen();
        $values = [
            'user_id' => new ObjectId($this->user_id),
            'username' => $this->username,
            'token_key' => $this->token_key,
            'logged_time' => date('d-m-Y H:i:s')
        ];
        parent::create($values);
        if($this->errno == 0)$inserted = true;
        return $inserted;
    }

    //Delete a token(when an user logout)
    public function token_delete(array $filter): bool{
        $deleted = false;
        $this->errno = 0;
        parent::delete($filter);
        if($this->errno == 0)$deleted = true;
        return $deleted;
    }

    //Get a token(to check if user is olgged or not)
    public function token_get(array $filter): bool{
        $got = false;
        $this->errno = 0;
        $token = parent::get($filter);
        if($this->errno == 0){
            //Token with given filter found
            $this->id = $token["_id"];
            $this->user_id = $token["user_id"];
            $this->username = $token["username"];
            $this->token_key = $token["token_key"];
            $this->logged_time = $token["logged_time"];
            $got = true;
        }//if($this->errno == 0){
        return $got;
    }

    //Update the token
    public function token_update(array $filter, array $data): bool{
        $updated = false;
        $this->errno = 0;
        $this->keyGen();
        $data['$set']['token_key'] = $this->token_key;
        $data['$set']['logged_time'] = date('Y-m-d H:i:s');
        parent::update($filter,$data);
        if($this->errno == 0)$updated = true;
        return $updated;
    }
}
?>