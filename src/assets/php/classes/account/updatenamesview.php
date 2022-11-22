<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\UpdateNamesViewErrors as Unve;
use AngularBlog\Traits\MessageTrait;
use Exception;

class UpdateNamesView implements Unve{
    use MessageTrait;

    private ?UpdateNamesController $unc;

    public function __construct(?UpdateNamesController $unc)
    {
        if(!$unc) throw new Exception(Unve::NOUPDATENAMESCONTROLLERINSTANCE_EXC);
        $this->unc = $unc;
        $errnoUnc = $this->unc->getErrno();
        if($errnoUnc == 0)
            $this->done = true;
        $this->response_code = $this->unc->getResponseCode();
        $this->message = $this->unc->getResponse();
    }

    public function getController(){ return $this->unc; }
}
?>