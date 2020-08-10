<?php
namespace App\Controller;
use App\Model\Service\ServiceResponse;
use App\Repository\MailRepository;
use App\Model\Service\ServiceError;
use App\Builder\PasswordBuilder;

class MailsController extends AppController
{
    private $CurrentUser;
    private $UserRepository;
    private $MailRepository;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->MailRepository = new MailRepository($this->Mails);
        $this->UserRepository = $this->request->getParam('userRepository');
        $this->CurrentUser = $this->request->getParam('user');
    }

    public function add()
    {
        $response = new ServiceResponse();
        $this->request->allowMethod(['post']);

        $passwordBuilder=new PasswordBuilder();
        $toUserName = $this->request->getData('toUserName');
        $toUser = $this->UserRepository->GetByUserName($toUserName);
        if($toUser->isSuccessed){
            $newMailRecord = [
                "guid" => $passwordBuilder->GenerateToken(),
                "fromUserId" => $this->CurrentUser->id,
                "toUserId" => $toUser->data->id,
                "subject" => $this->request->getData('subject'),
                "content" => $this->request->getData('content'),
                "isActive" => true,
                "isDeleted" => false,
                "isRead" => false,
            ];
            $repositoryResult = $this->MailRepository->Create($newMailRecord);
            if($repositoryResult->isSuccessed){
                $response->isSuccessed=true;
                $response->data=$repositoryResult->data;
            }else{
                $response->isSuccessed=false;
                $response->errors=$repositoryResult->errors;
            }
        }else{
            $serviceError=new ServiceError();
            $serviceError->code="200003";
            $serviceError->message="To username is not found.";
            $serviceError->innerMessage="To username is not found.";
            $response->isSuccessed=false;
            $response->errors[]=$serviceError;
        }

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }

    public function read()
    {
        $response = new ServiceResponse();
        $this->request->allowMethod(['get']);
        $guid=$this->request->getParam('guid');

        $mail=$this->MailRepository->GetByGuid($guid);
        $response->isSuccessed=$mail->isSuccessed;
        if($mail->isSuccessed){
            $response->data=$mail->data;
            $response->data->fromUser=$this->UserRepository->GetById($mail->data->fromUserId);
            $response->data->fromUserName=$response->data->fromUser->data->userName;
            $response->data->toUser=$this->UserRepository->GetById($mail->data->toUserId);
            $response->data->toUserName=$response->data->toUser->data->userName;
            if(!$mail->data->isRead || $mail->data->toUserId == $this->CurrentUser->id){
                $mail->data->isRead=true;
                $this->MailRepository->Update($mail->data);
            }
        }

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }

    public function unreads()
    {
        $response = new ServiceResponse();
        $this->request->allowMethod(['get']);

        $unreadMails=$this->MailRepository->ListUnreadMails($this->CurrentUser->id);
        $response->isSuccessed=$unreadMails->isSuccessed;
        if($unreadMails->isSuccessed) $response->data=$unreadMails->data;

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }

    public function numberOfUnreads()
    {
        $response = new ServiceResponse();
        $this->request->allowMethod(['get']);

        $unreadMails=$this->MailRepository->ListUnreadMails($this->CurrentUser->id);
        $response->isSuccessed=$unreadMails->isSuccessed;
        if($unreadMails->isSuccessed) $response->data=count($unreadMails->data);

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }

    public function list()
    {
        $response = new ServiceResponse();
        $this->request->allowMethod(['get']);
        $page=$this->request->getParam('page')+1;
        $pageSize=$this->request->getParam('pageSize');

        $allMails=$this->MailRepository->ListForUser($page, $pageSize, $this->CurrentUser->id);
        $response->isSuccessed=$allMails->isSuccessed;
        if($allMails->isSuccessed) $response->data=$allMails->data;

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }
}