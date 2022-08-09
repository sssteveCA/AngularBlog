<?php

namespace AngularBlog\Traits;


//This trait contains common error properties and methods for most classes
trait ErrorTrait{
    protected int $errno = 0;
    protected ?string $error = null;

    public function getErrno(): int{return $this->errno;}
}

?>