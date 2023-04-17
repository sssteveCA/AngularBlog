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
        if($this->response_code == 200){
            $this->done = true;
            $this->message_array = $this->guac->getResponseArray();
        }   
        if($this->guac->getErrno() == 0){
            $this->foundActions = true;
            $this->message_array['actions'] = $this->setActionsList($this->message_array['actions']);
        }     
    }

    public function areActionsFound(){ return $this->foundActions; }

    /**
     * Convert the array of objects into array of arrays
     */
    private function setActionsList($actionslist): array{
        $al = [];
        foreach($actionslist as $action){
            $al[] = [
                'id' => $action->getId(),
                'action_date' => date('d-m-Y H:i:s',(int)$action->getActionDate()),
                'description' => $action->getDescription(),
                'title' => $action->getTitle()
            ];
        }
        return $al;
    }
}
?>