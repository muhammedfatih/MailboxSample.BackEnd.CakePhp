<?php
namespace App\Middleware;
use Cake\ORM\TableRegistry;
use App\Repository\UserRepository;


class AuthorizationMiddleware{
    public function __invoke($request, $response, $next)
    {
        $userModel = TableRegistry::get('Users');
        $userRepository = new UserRepository($userModel);

        $requestHeaders = $request->getHeaders();
        $hasAuthorizationHeader = array_key_exists("Authorization", $requestHeaders);
        $response->statusCode(401);

        if($hasAuthorizationHeader){
            $bearerToken=trim(str_replace("Basic ", "", $requestHeaders["Authorization"][0]));
            $credentials=explode(':', base64_decode($bearerToken));
            if(count($credentials) == 2){
                $userName=$credentials[0];
                $token=$credentials[1];
                $user=$userRepository->GetValidUserByToken($userName, $token);
                if($user->isSuccessed){
                    $response->statusCode(200);
                    $request = $request->withParam('user', $user->data);
                    $request = $request->withParam('userRepository', $userRepository);
                    $response = $next($request, $response);
                }
            }
        }
        return $response;
    }
}