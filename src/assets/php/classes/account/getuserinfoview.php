<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\GetUserInfoViewErrors as Guive;
use AngularBlog\Traits\MessageArrayTrait;
use Exception;

class GetUserInfoView implements Guive{

    use MessageArrayTrait;

    private ?GetUserInfoController $guic;

    public function __construct(GetUserInfoController $guic)
    {
        if(!$guic) throw new Exception(Guive::NOGETUSERINFOCONTROLLERINSTANCE_EXC);
        $this->guic = $guic;
        if($this->guic->getErrno() == 0){
            $this->done = true;
            $this->message_array = $this->guic->getResponseArray();
        }
        $this->response_code = $this->guic->getResponseCode();

    }

}

?>