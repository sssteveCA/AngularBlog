<?php

namespace AngularBlog\Classes\Account\Info;

use AngularBlog\Classes\Token;
use AngularBlog\Traits\ErrorTrait;
use AngularBlog\Interfaces\Account\Info\GetUsernameControllerErrors as Guce;
use AngularBlog\Traits\ResponseTrait;
use AngularBlog\Interfaces\Constants as C;

class GetUsernameController implements Guce{
    use ErrorTrait, ResponseTrait;

    private ?Token $token;
    private static string $logFile = C::FILE_LOG;

    public function __construct(array $data)
    {
        $this->checkValues($data);
    }

    private function checkValues(array $data){
        if(!isset($data['token'])) throw new \Exception(Guce::NOTOKENINSTANCE_EXC);
    }

    private function getUsername(): bool{
        return false;
    }
}
?>