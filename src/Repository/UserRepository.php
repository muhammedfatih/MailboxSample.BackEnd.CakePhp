<?php
namespace App\Repository;
use App\Model\Service\ServiceError;

class UserRepository extends BaseRepository{
    public function __construct ($model){
        parent::__construct ($model);
    }
    public function GetValidUserByToken($username, $token){
        $dbResult = $this->Model->find()->where(['userName' => $username, 'token'=>$token])->first();
        $this->Response->isSuccessed=$dbResult!=null;
        $this->Response->data = $dbResult; 
        if(!$this->Response->isSuccessed){
            $serviceError=new ServiceError();
            $serviceError->code="000002";
            $serviceError->message='Token is invalid.';
            $serviceError->innerMessage='Token is invalid.';
            $this->Response->errors[] = $serviceError;
        }
        return $this->Response;
    }
    public function GetValidUser($username, $password){
        $dbResult=$this->Model->find()->where(['userName' => $username, 'password'=>$password])->first();
        $this->Response->isSuccessed=$dbResult!=null;
        $this->Response->data = $dbResult; 
        if(!$this->Response->isSuccessed){
            $serviceError=new ServiceError();
            $serviceError->code="000003";
            $serviceError->message='Credential is invalid.';
            $serviceError->innerMessage='Credential is invalid.';
            $this->Response->errors[] = $serviceError;
        }
        return $this->Response;

    }
    public function GetByUserName($username){
        $dbResult = $this->Model->find()->where(['userName' => $username])->first();
        $this->Response->isSuccessed=$dbResult!=null;
        $this->Response->data = $dbResult; 
        if(!$this->Response->isSuccessed){
            $serviceError=new ServiceError();
            $serviceError->code="000004";
            $serviceError->message='Username is invalid.';
            $serviceError->innerMessage='Username is invalid.';
            $this->Response->errors[] = $serviceError;
        }
        return $this->Response;
    }
}