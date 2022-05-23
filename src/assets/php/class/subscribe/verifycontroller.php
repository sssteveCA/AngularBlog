<?php

namespace AngularBlog\Classes\Subscribe;

use AngularBlog\Classes\User;
use AngularBlog\Interfaces\ModelErrors as Me;
use AngularBlog\Interfaces\Subscribe\VerifyControllerErrors as Vce;
use AngularBlog\Interfaces\Constants as C;

class VerifyController implements Vce,Me,C{
    private ?User $user;
    private string $response = "";
    private int $errno = 0;
    private ?string $error = null;

    public function __construct(?User $user)
    {
        if(!$user)throw new \Exception(Vce::NOUSERINSTANCE_EXC);
        $this->user = $user;
        $this->active();
        $this->setResponse();
    }

    public function getResponse(){$this->response;}
    public function getErrno(){return $this->errno;}
    public function getError(){
        switch($this->error){
            default:
                case Vce::DATANOTSET:
                    $this->error = Vce::DATANOTSET_MSG;
                    break;
                case Vce::FROMUSER:
                    $this->error = Vce::FROMUSER_MSG;
                    break;
                $this->error = null;
                break;
        }
        return $this->error;
    }

    //complete registration and activate account
    private function active(): bool{
        $ok = false;
        $this->errno = 0;
        $emailVerif = $this->user->getEmailVerif();
        if(isset($emailVerif)){
            $lastMod = date('Y-m-d H:i:s');
            $updateFilter = [
                '$and' => [
                    ['emailVerif' => $emailVerif],
                    ['subscribed' => false]]
            ];
            $updateSet = [
                '$set' => ['emailVerif' => null, 'last_modified' => $lastMod,'subscribed' => true] 
             ];
             $update = $this->user->update($updateFilter,$updateSet);
             if($update){
                 $ok = true;
             }
             else $this->errno = Vce::FROMUSER;
        }//if(isset($emailVerif)){
        else $this->errno = Vce::DATANOTSET;
        return $ok;
    }

    //Set the response to send to the view
    private function setResponse(){
        switch($this->errno){
            case 0:
                $this->response = C::ACTIVATION_OK;
                break;
            case Vce::FROMUSER:
                $errnoU = $this->user->getErrno();
                switch($errnoU){
                    case Me::NOTUPDATED:
                        $this->response = C::ACTIVATION_INVALID_CODE;
                        break;
                    default:
                        $this->response = C::ACTIVATION_ERROR;
                        break;
                }
                break;
            default:
                $this->response = C::ACTIVATION_ERROR;
                break;
        }
    }
}
?>