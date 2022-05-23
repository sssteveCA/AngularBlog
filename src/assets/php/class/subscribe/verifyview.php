<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\Subscribe\VerifyController;
use AngularBlog\Interfaces\Subscribe\VerifyViewErrors as Vve;
use AngularBlog\Interfaces\Constants as C;

class VerifyView implements Vve,C{
    private ?VerifyController $vc;
    private string $message = "";
    private static string $logFile = C::FILE_LOG;

    public function __construct(?VerifyController $vc)
    {
        if(!$vc)throw new \Exception(Vve::NOVERIFYCONTROLLERINSTANCE_EXC);
        $this->vc = $vc;
        //file_put_contents(VerifyView::$logFile,"VerifyController => ".var_export($this->vc,true)."\r\n",FILE_APPEND);
        file_put_contents(VerifyView::$logFile,"VerifyView response => ".var_export($this->vc->getResponse(),true)."\r\n",FILE_APPEND);
        $this->message = $this->vc->getResponse();
        file_put_contents(VerifyView::$logFile,"Message => {$this->message}\r\n",FILE_APPEND);     
    }

    public function getMessage(){return $this->message;}
}

?>