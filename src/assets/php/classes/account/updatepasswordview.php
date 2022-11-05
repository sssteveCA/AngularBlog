<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Account\UpdatePasswordViewErrors as Upve;
use Exception;

class UpdatePasswordView implements Upve{
    use MessageTrait;

    private ?UpdatePasswordController $upc;

    public function construct(?UpdatePasswordController $upc){
        if(!$upc) throw new Exception(Upve::NOUPDATEPASSWORDCONTROLLERINSTANCE_EXC);
        $this->upc = $upc;
        $errnoUpc = $this->upc->getErrno();
        if($errnoUpc == 0)
            $this->done = true;
        $this->message = $this->upc->getResponse();
    }

    public function getController(){return $this->upc;}
}
?>