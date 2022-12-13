<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\DeleteAccountViewErrors as Dave;
use AngularBlog\Traits\MessageTrait;
use Exception;

class DeleteAccountView implements Dave{
    use MessageTrait;

    private ?DeleteAccountController $dac;

    public function __construct(?DeleteAccountController $dac)
    {
        if(!$dac) throw new Exception(Dave::NODELETEACCOUNTCONTROLLERINSTANCE_EXC);
        $this->dac = $dac;
        $errnoDac = $this->dac->getErrno();
        if($errnoDac == 0)
            $this->done = true;
        $this->response_code = $this->dac->getResponseCode();
        $this->message = $this->dac->getResponse();
    }

    public function getController(){ return $this->dac; }
}
?>