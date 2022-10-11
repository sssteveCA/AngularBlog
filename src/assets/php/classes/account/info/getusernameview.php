<?php

namespace AngularBlog\Classes\Account\Info;

use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Account\Info\GetUsernameViewErrors as Guve;
use AngularBlog\Traits\MessageTrait;

class GetUsernameView implements Guve{
    use MessageTrait;

    private ?GetUsernameController $guc;

    public function __construct(?GetUsernameController $guc)
    {
        if(!$guc)throw new \Exception(Guve::NOGETUSERNAMECONTROLLERINSTANCE_EXC);
        $this->guc = $guc;
        $this->message = $this->guc->getResponse();
    }
}
?>