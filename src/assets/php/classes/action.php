<?php

namespace AngularBlog\Classes;

class Action extends Model{
    private ?string $id;
    private ?string $user_id;
    private ?string $action_date;
    private ?string $title;
    private ?string $description;

    public static array $regex = array(
        'time' => '/^[0-9]{4}-(0[1-9]|1[0-2])-([012][0-9]|3[01])\s+([0-1][0-9]|2[0-3])(:[0-5][0-9]){2}$/i'
    );

    public function __construct(array $data = array()){
        $data['connection_url'] = isset($data['connection_url']) ? $data['connection_url']: $_ENV['MONGODB_CONNECTION_STRING'];
        $data['database_name'] = isset($data['database_name']) ? $data['database_name']: $_ENV['MONGODB_DATABASE'];
        $data['collection_name'] = isset($data['collection_name']) ? $data['collection_name']: $_ENV['MONGODB_COLLECTION_ACTIONS'];
        parent::__construct($data);
        $this->id = isset($data['id']) ? $data['id']:null;
        $this->user_id = isset($data['user_id']) ? $data['user_id']:null;
        $this->action_date = isset($data['action_date']) ? $data['action_date']:null;
        $this->title = isset($data['title']) ? $data['title']:null;
        $this->description = isset($data['description']) ? $data['description']:null;
    }

    public function getId(){return $this->id;}
    public function getUserId(){return $this->user_id;}
    public function getActionDate(){return $this->action_date;}
    public function getTitle(){return $this->title;}
    public function getDescription(){return $this->description;}

    public function action_create(): bool{
        $this->errno = 0;
        $this->action_date = date('Y-m-d H:i:s');
        if($this->validate()){
            $values = [
                'user_id' => $this->user_id,
                'action_date' => $this->action_date,
                'title' => $this->title,
                'description' => $this->description
            ];
            parent::create($values);
            if($this->errno == 0) return true;
            return false;
        }//if($this->validate()){
        return false;
    }

    /**
     * check if properties are all valid before insert
     */
    private function validate(): bool{
        if(isset($this->action_date) && !preg_match(Action::$regex["action_date"],$this->action_date))
            return false;
        return true;
    }
}
?>