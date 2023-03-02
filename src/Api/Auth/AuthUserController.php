<?php
namespace WBCrypto\Api\Auth;

use WBCrypto\Api\Model\UsersModel as UsersModel;
use Pecee\Http\Request;


    class AuthUserController
    {
        private $login;
        private $passwordRequest;

        public function __construct(UsersModel $user,Request $request)
        {
            $this->user = $user;
            $this->request = $request;
        }

        public function userLoginValidate()
        {
            if(!$this->request->getUser() || !$this->request->getPassword()){
                $result['error'] = "Digite usuário (CPF/CNPJ) e senha para prosseguir";
                echo json_encode($result);
                die();
            }

            try{
                $this->login = $this->request->getUser();
                $this->passwordRequest = $this->request->getPassword();

                $user = $this->user->getUserByTaxVat(($this->login));

                $hash = $user['password'];

                if(password_verify($this->passwordRequest, $hash)){
                    return true;
                }else {
                    return false;
                    die();
                }
            }catch (\Exception $e){
                $result['error'] = "Não foi possível realizar a operação: " . $e;
            }
        }
    }