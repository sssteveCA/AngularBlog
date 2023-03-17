<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Interfaces\Action\GetUserActionsViewErrors as Guave;
use AngularBlog\Traits\MessageArrayTrait;

class GetUserActionsView implements Guave{

    use MessageArrayTrait; 

    private ?GetUserActionsController $guac;
    private bool $foundActions = false;
    private bool $emptyList = false;

    public function __construct(?GetUserActionsController $guac )
    {
        if(!$guac) throw new \Exception(Guave::NOGETUSERACTIONSCONTROLLERINSTANCE_EXC);
        $this->guac = $guac;
        $this->response_code = $this->guac->getResponseCode();
        if($this->response_code == 200)
            $this->done = true;
        if($this->guac->getErrno() == 0)
            $this->foundActions = true;
        else
            $this->message_array = $this->guac->getResponseArray();
            
    }

    public function areActionsFound(){ return $this->foundActions; }
}
?>