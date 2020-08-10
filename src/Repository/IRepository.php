<?php
namespace App\Repository;

interface IRepository{
    public function Create($obj);
    public function GetById($id);
    public function GetByGuid($guid);
    public function Update($obj);
    public function DeleteById($id);
    public function DeleteByGuid($guid);
    public function List($page, $pageSize);
}