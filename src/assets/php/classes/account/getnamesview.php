<?php

namespace AngularBlog\Classes\Account;

class GetNamesView{

    private ?GetNamesController $gnc;

    public function __construct(GetNamesController $guc)
    {
        $this->guc = $guc;
    }
}
?>