<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\UserAuthorizedViewErrors as Uave;
use AngularBlog\Traits\MessageTrait;
use Exception;

class UserAuthorizedView implements Uave{
    use MessageTrait;

    private ?UserAuthorizedController $uac;

    public function __construct(?UserAuthorizedController $uac)
    {
        if(!$uac)throw new Exception(Uave::NOUSERAUTHORIZEDCONTROLLERINSTANCE_EXC);
        $this->uac = $uac;
        $errnoUac = $this->uac->getErrno();
        if($errnoUac == 0)
            $this->done = true;
        $this->message = $this->uac->getResponse();
    }

    public function getController(){return $this->uac;}
}
?>