<?php

namespace AngularBlog\Traits;


//This trait contains common error properties and methods for most classes
trait ErrorTrait{
    private int $errno = 0;
    private ?string $error = null;

    public function getErrno(): int{return $this->errno;}
}

?>