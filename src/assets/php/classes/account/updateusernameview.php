<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Account\UpdateUsernameViewErrors as Uuve;

class UpdateUsernameView implements Uuve{
    use MessageTrait;

    private ?UpdateUsernameController $uuc;

    public function __construct(?UpdateUsernameController $uac){
    
    }
}
?>