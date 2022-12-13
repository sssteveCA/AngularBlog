<?php

namespace AngularBlog\Classes\Account;

use AngularBlog\Interfaces\Account\DeleteAccountControllerErrors as Dace;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Traits\ResponseTrait;

class DeleteAccountController implements Dace{
    use ErrorTrait, ResponseTrait;
}
?>