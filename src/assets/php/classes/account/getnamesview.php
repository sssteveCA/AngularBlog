<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\GetNamesViewErrors as Gnve;
use AngularBlog\Traits\MessageArrayTrait;

class GetNamesView implements Gnve{

    use MessageArrayTrait;

    private ?GetNamesController $gnc;

    public function __construct(GetNamesController $gnc)
    {
        $this->gnc = $gnc;
        if($this->gnc->getErrno() == 0){}
    }
}
?>