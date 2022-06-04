<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Classes\Model;

//This class is used to store info about logged users

class Token extends Model implements C{
    private ?string $id;
    private ?string $user_id; //Id of logged user

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: C::MONGODB_CONNECTION_STRING;
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: C::MONGODB_DATABASE;
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: C::MONGODB_COLLECTION_ARTICLES;
        parent::__construct($data);
    }

    public function getId(){return $this->id;}
    public function getUserId(){return $this->user_id;}

    //Insert a new Token(when an used sign in)
    public function token_create(): bool{
        $inserted = false;
        $this->errno = 0;
        $values = [
            'user_id' => $this->user_id
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
}
?>