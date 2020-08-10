<?php
namespace App\Converter;
use App\Model\Service\ServiceError;

class ValidationErrorConverter{
    public function Convert($validationError){
        $errors=[];
        foreach($validationError as $validationErrorKey => $validationErrorValue){
            foreach($validationErrorValue as $validationErrorValueKey => $validationErrorValueValue){
                $serviceError=new ServiceError();
                $serviceError->code="000001";
                $serviceError->innerMessage=$validationErrorValueValue;
                $serviceError->message=$validationErrorKey;
                $errors[]=$serviceError;
            }
        }
        return $errors;
    }
}