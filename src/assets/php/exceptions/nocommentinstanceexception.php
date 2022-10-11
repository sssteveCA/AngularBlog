<?php

namespace AngularBlog\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when Comment instance provided is null
 */
class NoCommentInstanceException extends Exception{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>