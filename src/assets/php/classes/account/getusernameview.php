<?php

namespace AngularBlog\Classes\Info;

use AngularBlog\Classes\Account\GetUsernameController;
use AngularBlog\Interfaces\Account\GetUsernameViewErrors as Guve;
use AngularBlog\Traits\MessageTrait;

class GetUsernameView implements Guve{
    use MessageTrait;

    private ?GetUsernameController $guc;

    public function __construct(?GetUsernameController $guc)
    {
        if(!$guc)throw new \Exception(Guve::NOGETUSERNAMECONTROLLERINSTANCE_EXC);
        $this->guc = $guc;
        if($this->guc->getErrno() == 0)
            $this->done = true;
        $this->response_code = $this->guc->getResponseCode();
        $this->message = $this->guc->getResponse();
    }
}
?>