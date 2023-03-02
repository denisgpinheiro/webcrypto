<?php
namespace WBCrypto\Middlewares;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use WBCrypto\Api\Auth\AuthUserController as AuthUser;
use WBCrypto\Api\Model\UsersModel as UsersModel;

class AuthMiddleware implements IMiddleware
    {
        public function __construct(UsersModel $user,Request $request, AuthUser $authUser)
        {
            $this->user = $user;
            $this->request = $request;
            $this->authUser = $authUser;
        }

        public function handle(Request $request): void
        {
            try {

               $this->request = $this->authUser->userLoginValidate();

               if($this->request == false){
                   // PENSAR EM LANÇAR UMA EXCEPTION AQUI
                   $result['error'] = "Usuário não autenticado";
                   echo json_encode($result);
                   die();
               }
            }catch (\Exception $e){
                response()
                    ->httpCode(400)
                    ->json([
                        'message' => $e->getMessage()
                    ]);
            }
        }
    }