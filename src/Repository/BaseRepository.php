<?php
namespace App\Repository;
use App\Model\Service\ServiceResponse;
use App\Converter\ValidationErrorConverter;

abstract class BaseRepository implements IRepository{
    protected $Model;
    protected $Response;
    public function __construct($model){
        $this->Model=$model;
        $this->Response=new ServiceResponse();
    }
    public function Create($obj){
        $obj = $this->Model->newEntity($obj);
        $dbResult = $this->Model->save($obj);
        $this->Response->data=$obj;
        $this->Response->isSuccessed=$dbResult;
        if(!$dbResult){
            $validationErrorConverter= new ValidationErrorConverter();
            $this->Response->errors = $validationErrorConverter->Convert($obj->errors());
        }
        return $this->Response;
    }
    public function GetById($id){
        $this->Response->isSuccessed=true;
        $this->Response->data=$this->Model->get($id);
        return $this->Response;
    }
    public function GetByGuid($guid){
        $this->Response->isSuccessed=true;
        $this->Response->data=$this->Model->find()->where(['guid' => $guid])->first();
        return $this->Response;
    }
    public function Update($obj){
        $obj = $this->Model->patchEntity($obj,  $obj->toArray());
        $dbResult=$this->Model->save($obj);
        $dbResultBool= $dbResult !== false;
        $this->Response->data= $obj;
        $this->Response->isSuccessed=$dbResultBool;
        if(!$dbResult){
            $validationErrorConverter= new ValidationErrorConverter();
            $this->Response->errors = $validationErrorConverter->Convert($obj->errors());
        }
        return $this->Response;
    }
    public function DeleteById($id){
        $obj = $this->Model->get($id);
        $this->Response->isSuccessed=true;
        if (!$this->Model->delete($obj)) {
            $this->Response->isSuccessed=false;
            $this->Response->errors[] = "Record can not deleted because of a database error.";
        }
        return $this->Response;
    }
    public function DeleteByGuid($guid){
        $obj = $this->Model->find()->where(['guid' => $guid])->first();
        $this->Response->isSuccessed=true;
        if (!$this->Model->delete($obj)) {
            $this->Response->isSuccessed=false;
            $this->Response->errors[] = "Record can not deleted because of a database error.";
        }
        return $this->Response;
    }
    public function List($page, $pageSize){
        $this->Response->data=$this->Model->find()->limit($pageSize)->page($page)->all();
        $this->Response->isSuccessed=true;
        return $this->Response;
    }
}