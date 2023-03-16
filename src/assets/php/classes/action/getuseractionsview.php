<?php

namespace AngularBlog\Classes\Action;

use AngularBlog\Traits\MessageTrait;

class GetUserActionsView{

    use MessageTrait;

    private ?GetUserActionsController $guac;
    private bool $foundActions = false;
    private bool $emptyList = false;

    public function __construct(?GetUserActionsController $guac )
    {
        
    }

    public function actionsFound(){ return $this->foundActions; }
}
?>