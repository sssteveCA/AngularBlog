<?php

namespace AngularBlog\Traits;

trait ResponseTrait{
    /**
     * The HTTP response status code
     */
    private int $response_code = 200;
    /**
     * The response string message to send to the view
     */
    private string $response = "";

    public function getResponseCode(){return $this->response_code;}
    public function getResponse(){return $this->response;}
}
?>