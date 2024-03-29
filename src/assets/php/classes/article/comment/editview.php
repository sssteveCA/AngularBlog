<?php

namespace AngularBlog\Classes\Article\Comment;

use AngularBlog\Interfaces\Constants as C;
use AngularBlog\Traits\MessageTrait;
use AngularBlog\Classes\Article\Comment\EditController;
use AngularBlog\Interfaces\Article\Comment\EditViewErrors as Eve;


class EditView implements Eve{
    use MessageTrait;

    private ?EditController $ec;
    private static string $logFile = C::FILE_LOG;

    public function __construct(?EditController $ec)
    {
        if(!$ec)throw new \Exception(Eve::NOEDITCONTROLLERINSTANCE_EXC);
        $this->ec = $ec;
        $errnoEc = $this->ec->getErrno();
        if($errnoEc == 0)
            $this->done = true;
        $this->response_code = $this->ec->getResponseCode();
        $this->message = $this->ec->getResponse();
    }

    public function getController(){return $this->ec;}
}

?>