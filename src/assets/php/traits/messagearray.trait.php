<?php

namespace AngularBlog\Traits;

/**
 * Use by views classes that can receive a response in array format from controller
 */
trait MessageArrayTrait{

    use MessageTrait;

    private array $message_array = [];

    public function getMessageArray(){ return $this->message_array; }
}
?>