<?php
namespace App\Builder;

class PasswordBuilder{
    public function Encrypt($inputString){
        return hash('sha256', $inputString);
    }
    public function GenerateToken(){
        return uniqid();
    }
}