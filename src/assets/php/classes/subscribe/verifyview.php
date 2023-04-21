<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Interfaces\Subscribe\VerifyViewErrors as Vve;
use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Traits\MessageTrait;

class VerifyView implements Vve,C{

    use MessageTrait; 

    private ?VerifyController $vc;
    private static string $logFile = C::FILE_LOG;

    public function __construct(?VerifyController $vc)
    {
        if(!$vc)throw new \Exception(Vve::NOVERIFYCONTROLLERINSTANCE_EXC);
        $this->vc = $vc;
        $this->response_code = $this->vc->getResponseCode();
        $this->message = $this->vc->getResponse();
    }

}

?>