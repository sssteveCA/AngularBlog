<?php

namespace AngularBlog\Classes;

use AngularBlog\Interfaces\ModelsErrors as Me;

class ActionList extends Models implements Me{

    private array $results = [];

    public function __construct(array $data = array())
    {
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: $_ENV['MONGODB_CONNECTION_STRING'];
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: $_ENV['MONGODB_DATABASE'];
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: $_ENV['MONGODB_COLLECTION_ACTIONS'];
        parent::__construct($data);
    }

    public function getResults():array {return $this->results;}
    public function getError(){
        if($this->errno <= Me::MODELS_RANGE_MAX){
            return parent::getError();
        }
        else{
            switch($this->errno){
                default:
                    $this->error = null;
                    break;
            }
        }
        return $this->error;
    }
}
?>