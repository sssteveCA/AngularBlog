<?php

namespace AngularBlog\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when Token instance provided is null
 */
class NoTokenInstanceException extends Exception{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>