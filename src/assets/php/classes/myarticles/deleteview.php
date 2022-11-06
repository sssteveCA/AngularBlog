<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\MyArticles\DeleteViewErrors as Dve;
use AngularBlog\Traits\MessageTrait;

class DeleteView implements Dve{

    use MessageTrait;

    private ?DeleteController $dc;
 

    public function __construct(?DeleteController $dc){
        if(!$dc)throw new \Exception(Dve::NODELETECONTROLLERINSTANCE_EXC);
        $this->dc = $dc;
        $errnoDc = $this->dc->getErrno();
        if($errnoDc == 0)
            $this->done = true;
        $this->response_code = $this->dc->getResponseCode();
        $this->message = $this->dc->getResponse();
    }

    public function getController(){return $this->dc;}
}
?>