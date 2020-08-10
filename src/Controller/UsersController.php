<?php
namespace App\Controller;
use App\Model\Service\ServiceResponse;
use App\Model\Service\ServiceError;
use App\Repository\UserRepository;
use App\Builder\PasswordBuilder;

class UsersController extends AppController
{
    private $UserRepository;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->UserRepository=new UserRepository($this->Users);

    }

    public function add()
    {
        $response = new ServiceResponse();
        $passwordBuilder=new PasswordBuilder();
        $this->request->allowMethod(['post']);

        $userName=$this->request->getData("userName");
        $this->request->data["token"]=$passwordBuilder->GenerateToken();
        $previouslyCreatedUser=$this->UserRepository->GetByUserName($userName)->data;
        if($previouslyCreatedUser == null){
            $this->request->data["guid"]=$passwordBuilder->GenerateToken();
            $this->request->data["isActive"]=true;
            $this->request->data["isDeleted"]=false;
            $repositoryResult = $this->UserRepository->Create($this->request->getData());
            if($repositoryResult->isSuccessed){
                $response->isSuccessed=true;
                $response->data=true;
            }else{
                $response->isSuccessed=false;
                $response->errors=$repositoryResult->errors;
            }
        }else{
            $serviceError=new ServiceError();
            $serviceError->code="100003";
            $serviceError->message="Username";
            $serviceError->innerMessage="Username is already in use.";
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

    public function login()
    {
        $response = new ServiceResponse();
        $passwordBuilder=new PasswordBuilder();
        $this->request->allowMethod(['post']);

        $userName=$this->request->getData("userName");
        $password=$this->request->getData("password");
        $passwordEncrypted=$passwordBuilder->Encrypt($password);
        $this->request->data["password"]=$passwordEncrypted;

        $previouslyCreatedUser=$this->UserRepository->GetValidUser($userName, $passwordEncrypted)->data;
        if($previouslyCreatedUser == null){
            $serviceError=new ServiceError();
            $serviceError->code="100001";
            $serviceError->message="Invalid credentials.";
            $serviceError->innerMessage="Username or password is invalid.";
            $response->isSuccessed=false;
            $response->errors[]=$serviceError;
        }else{
            $newToken=$passwordBuilder->GenerateToken();
            $this->request->data["id"]=$previouslyCreatedUser->id;
            $this->request->data["token"]=$newToken;
            $previouslyCreatedUser->Token=$newToken;

            $repositoryResult = $this->UserRepository->Update($previouslyCreatedUser);//$this->request->getData());
            if($repositoryResult->isSuccessed){
                $response->isSuccessed=true;
                $response->data=$repositoryResult->data;
            }else{
                $response->isSuccessed=false;
                $response->errors=$repositoryResult->errors;
            }
        }

        $this->set([
            'isSuccessed' => $response->isSuccessed,
            'data' => $response->data,
            'errors' => $response->errors,
            '_serialize' => ['isSuccessed', 'data', 'errors']
        ]);
    }
}