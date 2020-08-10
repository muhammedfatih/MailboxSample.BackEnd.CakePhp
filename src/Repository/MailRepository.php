<?php
namespace App\Repository;

class MailRepository extends BaseRepository{
    public function ListForUser($page, $pageSize, $toUserId){
        $this->Response->isSuccessed=true;
        $this->Response->data = $this->Model->find()->where(['toUserId' => $toUserId])->limit($pageSize)->page($page)->all();
        return $this->Response;
    }
    public function ListUnreadMails($toUserId){
        $this->Response->isSuccessed=true;
        $this->Response->data = $this->Model->find()->where(['toUserId' => $toUserId, 'isRead'=>false, 'isDeleted'=>false, 'isActive'=>true])->order(['createdAt'=>'ASC'])->all();
        return $this->Response;
    }
}