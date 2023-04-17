<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Interfaces\Action\DeleteUserActionViewErrors as Duave;
use AngularBlog\Traits\MessageTrait;
use MongoDB\Operation\Delete;

class DeleteUserActionView implements Duave{

    use MessageTrait;

    private ?DeleteUserActionController $duac;

    public function __construct(?DeleteUserActionController $duac)
    {
        $this->duac = $duac;
        if($this->duac->getErrno() == 0)
            $this->done = true;
            $this->response_code = $this->duac->getResponseCode();
            $this->message = $this->duac->getResponse();
    }

    public function getController(){return $this->duac;}

}
?>