<?php

namespace AngularBlog\Traits;

/**
 * Controllers that must send multiple data as response
 */
trait ResponseMultipleTrait{

    use ResponseTrait;

    /**
     * The response array with mutiple values
     */
    private array $response_array = [];

    public function getResponseArray(){ return $this->response_array; }

}
?>