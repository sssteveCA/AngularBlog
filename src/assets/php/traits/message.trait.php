<?php

namespace AngularBlog\Traits;

//This trait is added to the Views classes for message and for checking nresult of the controller operations
trait MessageTrait{
    private string $message = "";
    private bool $done = false; //true if operations are done successfully

    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}

?>