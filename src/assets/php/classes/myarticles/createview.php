<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;

class CreateView implements Cve{
    private ?CreateController $cc;
    private string $message = "";
    private bool $done = false; //true if article creation is done successfully

    public function __construct(?CreateController $cc)
    {
        if(!$cc)throw new \Exception(Cve::NOCREATECONTROLLERINSTANCE_EXC);
        $this->cc = $cc;
        $errnoCc = $this->cc->getErrno();
        if($errnoCc == 0)
            $this->done = true;
        $this->message = $this->cc->getResponse();
    }

    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}
?>