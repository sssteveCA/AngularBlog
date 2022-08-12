<?php

namespace AngularBlog\Traits;

//This trait is used by 'Authorized' classes
trait AuthorizedTrait{
    private bool $authorized = false;

    public function isAuthorized(){return $this->authorized;}
}
?>