<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Model;

//This class is used to store info about logged users

class Token extends Model implements C{
    private ?string $id;
    private ?string $user_id; //Id of logged user
    private ?string $username; //Username of logged user
    private ?string $token_key; //generated unique key when user log in
    private ?string $logged_time; //Date when specific user has logged
    private static int $key_length = 80; //Token key string length

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
        $this->user_id = isset($data['user_id'])? $data['user_id']: null;
        $this->username = isset($data['username'])? $data['username']: null;
        $this->keyGen();
    }

    public function getId(){return $this->id;}
    public function getUserId(){return $this->user_id;}
    public function getUsername(){return $this->username;}
    public function getTokenKey(){return $this->token_key;}
    public function getLoggedTime(){return $this->logged_time;}

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

    //Insert a new Token(when an used sign in)
    public function token_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $values = [
            'user_id' => $this->user_id,
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
        }//if($this->errno == 0){
        return $got;
    }

    //Update the token
    public function token_update(array $filter, array $data): bool{
        $updated = false;
        $this->errno = 0;
        $data['logged_time'] = date('Y-m-d H:i:s');
        parent::update($filter,$data);
        if($this->errno == 0)$updated = true;
        return $updated;
    }
}
?>