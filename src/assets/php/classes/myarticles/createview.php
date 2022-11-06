<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Classes\Myarticles\CreateController;
use AngularBlog\Interfaces\MyArticles\CreateViewErrors as Cve;
use AngularBlog\Traits\MessageTrait;

class CreateView implements Cve{

    use MessageTrait;

    private ?CreateController $cc;

    public function __construct(?CreateController $cc)
    {
        if(!$cc)throw new \Exception(Cve::NOCREATECONTROLLERINSTANCE_EXC);
        $this->cc = $cc;
        $errnoCc = $this->cc->getErrno();
        if($errnoCc == 0)
            $this->done = true;
        $this->response_code = $this->cc->getResponseCode();
        $this->message = $this->cc->getResponse();
    }

    public function getController(){return $this->cc;}
}
?>