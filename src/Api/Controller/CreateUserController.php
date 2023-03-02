<?php
namespace WBCrypto\Api\Controller;

use WBCrypto\Api\Model\UsersModel as UsersModel;
use WBCrypto\Api\Model\EnderecoModel as EnderecoModel;
use WBCrypto\Api\Model\ContaBancariaModel as ContaBancariaModel;

use Pecee\Http\Request;
use Pecee\Http\Url;
use Pecee\Http\Response;

    class CreateUserController
    {
        private $user;
        private $endereco;
        private $saldoInicial = 0;

        private $request;

        public function __construct(UsersModel $user, EnderecoModel $endereco, ContaBancariaModel $conta, Request $request)
        {
            $this->user = $user;
            $this->endereco = $endereco;
            $this->conta = $conta;
            $this->request = $request;
        }

        public function createNewUser()
        {
            $this->endereco->setRua($this->request->getInputHandler()->value('rua'));
            $this->endereco->setBairro($this->request->getInputHandler()->value('bairro'));
            $this->endereco->setCidade($this->request->getInputHandler()->value('cidade'));
            $this->endereco->setEstado($this->request->getInputHandler()->value('estado'));
            $this->endereco->setCep($this->request->getInputHandler()->value('cep'));
            $this->endereco->setNumero($this->request->getInputHandler()->value('numero'));

            if($this->endereco->addAddress(
                $this->endereco->getIdEndereco(),
                $this->endereco->getRua(),
                $this->endereco->getBairro(),
                $this->endereco->getCidade(),
                $this->endereco->getEstado(),
                $this->endereco->getNumero()
            )){
                $this->user->setIdEndereco($this->endereco->getIdEndereco());
            }

            $this->conta->setSaldoAtual($this->saldoInicial);

            if($this->conta->newAccount(
                $this->conta->getIdConta(),
                $this->conta->getNumeroConta()
            )){
                $this->user->setIdConta($this->conta->getIdConta());
            }

            $this->user->setNome($this->request->getInputHandler()->value('nome'));
            $this->user->setTaxVat($this->request->getInputHandler()->value('CPF/CNPJ'));
            $this->user->setRgDoc($this->request->getInputHandler()->value('rgDoc'));
            $this->user->setInscricaoEstadual($this->request->getInputHandler()->value('inscricaoEstadual'));
            $this->user->setDataNascimento($this->request->getInputHandler()->value('dataNascimento'));
            $this->user->setTelefone($this->request->getInputHandler()->value('telefone'));
            $this->user->setPassword(password_hash($this->request->getInputHandler()->value('senha'), PASSWORD_DEFAULT));

            if ($this->user->addUser(
                $this->user->getNome(),
                $this->user->getTaxVat(),
                $this->user->getRgDoc(),
                $this->user->getInscricaoEstadual(),
                $this->user->getDataNascimento(),
                $this->user->getTelefone(),
                $this->user->getPassword(),
                $this->user->getIdEndereco(),
                $this->user->getIdConta()
            )){
                $result['status'] = "Usuário cadastrado com sucesso!!";
            } else {
                $result['status'] = "Erro! Não foi possível adicionar o usuário.";
            }
            echo json_encode($result);
        }
    }