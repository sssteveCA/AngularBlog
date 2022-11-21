<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\GetNamesViewErrors as Gnve;
use AngularBlog\Traits\MessageArrayTrait;
use Exception;

class GetNamesView implements Gnve{

    use MessageArrayTrait;

    private ?GetNamesController $gnc;

    public function __construct(GetNamesController $gnc)
    {
        if(!$gnc) throw new Exception(Gnve::NOGETNAMESCONTROLLERINSTANCE_EXC);
        $this->gnc = $gnc;
        if($this->gnc->getErrno() == 0){
            $this->done = true;
            $this->message_array = $this->gnc->getResponseArray();
        }
        else{
            $this->message = $this->gnc->getResponse();
        }
        $this->response_code = $this->gnc->getResponseCode();
    }
}
?>