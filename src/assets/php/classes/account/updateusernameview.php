<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Account\UpdateUsernameViewErrors as Uuve;
use Exception;

class UpdateUsernameView implements Uuve{
    use MessageTrait;

    private ?UpdateUsernameController $uuc;

    public function __construct(?UpdateUsernameController $uuc){
        if(!$uuc)throw new Exception(Uuve::NOUPDATEUSERCONTROLLERINSTANCE_EXC);
        $this->uuc = $uuc;
        $errnoUuc = $this->uuc->getErrno();
        if($errnoUuc == 0)
            $this->done = true;
        $this->response_code = $this->uuc->getResponseCode();
        $this->message = $this->uuc->getResponse();
    }

    public function getController(){return $this->uuc;}
}
?>