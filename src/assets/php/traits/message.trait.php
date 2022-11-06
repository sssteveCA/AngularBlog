<?php

namespace AngularBlog\Traits;

//This trait is added to the Views classes for message and for checking nresult of the controller operations
trait MessageTrait{
    /**
     * The HTTP response status code
     */
    private int $response_code;
    /**
     * The message that informs the success or failure of the request
     */
    private string $message = "";
    /**
     * If true the request has been executed successfully, otherwise this variable is false
     */
    private bool $done = false; //true if operations are done successfully

    public function getResponseCode(){return $this->response_code;}
    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}

?>