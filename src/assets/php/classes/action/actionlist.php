<?php

namespace AngularBlog\Classes;

use AngularBlog\Classes\Action\Action;
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

    public function actionlist_get(array $filter, array $options = []): bool{
        $this->errno = 0;
        $cursor = parent::get($filter,$options);
        if($this->errno == 0){
            $results = $cursor->toArray();
            foreach($results as $action){
                $data = [
                    'id' => $action['_id'],
                    'user_id' => $action['user_id'],
                    'action_date' => $action['action_date'],
                    'title' => $action['title'],
                    'description' => $action['description']
                ];
                $this->results[] = new Action($data);
            }//foreach($results as $action){
            return true;
        }//if($this->errno == 0){
        return false;
    }
}
?>