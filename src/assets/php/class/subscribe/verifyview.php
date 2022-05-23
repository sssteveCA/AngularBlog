<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Interfaces\Subscribe\VerifyViewErrors as Vve;

class VerifyView implements Vve{
    private ?VerifyController $vc;
    private string $message;

    public function __construct(?VerifyController $vc)
    {
        if(!$vc)throw new \Exception(Vve::NOVERIFYCONTROLLERINSTANCE_EXC);
        $this->vc = $vc;
        $this->message = $this->vc->getResponse();
    }

    public function getMessage(){return $this->message;}
}

?>