<?php

namespace AngularBlog\Classes\Myarticles;

use AngularBlog\Interfaces\MyArticles\DeleteViewErrors as Dve;

class DeleteView implements Dve{
    private ?DeleteController $dc;
    private string $message = "";
    private bool $done = false; //true if article was deleted

    public function __construct(?DeleteController $dc){
        if(!$dc)throw new \Exception(Dve::NODELETECONTROLLERINSTANCE_EXC);
        $this->dc = $dc;
        $errnoDc = $this->dc->getErrno();
        if($errnoDc == 0)
            $this->done = true;
        $this->message = $this->dc->getResponse();
    }

    public function getMessage(){return $this->message;}
    public function isDone(){return $this->done;}
}
?>