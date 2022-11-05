<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Traits\MessageTrait;
use AngularBlog\Interfaces\Account\UpdatePasswordViewErrors as Upve;

class UpdatePasswordView implements Upve{
    use MessageTrait;
}
?>