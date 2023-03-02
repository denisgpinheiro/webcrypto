<?php
namespace WBCrypto\Api\Model;

use App\Model\LoggerModel;
use WBCrypto\Config\ConnectDatabase;
use PDO;
use WBCrypto\Api\Controller\LoggerController;

class UsersModel
    {
        public $id;
        public $nome;
        public $taxVat;
        public $rgDoc;
        public $inscricaoEstadual;
        public $dataNascimento;
        public $telefone;
        public $idEndereco;
        public $password;
        public $idConta;
        public $pdo;
        public $logger;

        public function __construct(ConnectDatabase $pdo, LoggerController $logger){
            $this->pdo = $pdo;
            $this->logger = $logger;
        }

        public function getId(){
            return $this->id;
        }

        public function getNome(){
            return $this->nome;
        }
        public function setNome($nome)
        {
            $this->nome = $nome;
        }

        public function getTaxVat(){
            return $this->taxVat;
        }
        public function setTaxVat($taxVat)
        {
            $this->taxVat = $taxVat;
        }

        public function getRgDoc(){
            return $this->rgDoc;
        }
        public function setRgDoc($rgDoc)
        {
            $this->rgDoc = $rgDoc;
        }

        public function getInscricaoEstadual(){
            return $this->inscricaoEstadual;
        }
        public function setInscricaoEstadual($inscricaoEstadual)
        {
            $this->inscricaoEstadual = $inscricaoEstadual;
        }

        public function getDataNascimento(){
            return $this->dataNascimento;
        }
        public function setDataNascimento($dataNascimento)
        {
            $this->dataNascimento = $dataNascimento;
        }

        public function getTelefone(){
            return $this->telefone;
        }
        public function setTelefone($telefone)
        {
            $this->telefone = $telefone;
        }

        public function getPassword(){
            return $this->password;
        }
        public function setPassword($password)
        {
            $this->password = $password;
        }

        public function getIdEndereco(){
            return $this->idEndereco;
        }
        public function setIdEndereco($idEndereco)
        {
            $this->idEndereco = $idEndereco;
        }

        public function getIdConta(){
            return $this->idConta;
        }
        public function setIdConta($idConta)
        {
            $this->idConta = $idConta;
        }

        public function getAllUsers(){

            $array = [];

            $sql = "SELECT * FROM correntistas ORDER BY id";
            $stmt = $this->pdo->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if($stmt) {
                foreach ($stmt as $data) {
                    $array[] = $data;
                }
            }else{
                $array['error'][] = "Nenhum usuário encontrado";
            }
            $this->logger->createLog('Listagem de Usuários');
            return $array;
        }

        public function addUser(
                $nome,
                $taxVat,
                $rgDoc,
                $inscricaoEstadual,
                $dataNascimento,
                $telefone,
                $password,
                $idEndereco,
                $idConta){
            $sql = "INSERT INTO correntistas (
                nome,
                taxVat, 
                rgDoc,
                inscricaoEstadual,
                dataNascimento, 
                telefone,
                password,
                idendereco,
                idconta) VALUES (
                :nome,
                :taxVat, 
                :rgDoc,
                :inscricaoEstadual,
                :dataNascimento, 
                :telefone,
                :password,
                :idEndereco,
                :idConta)"
            ;

            $stmt = $this->pdo->connect()->prepare($sql);

            if (!$stmt->execute([
                ':nome' => $nome,
                ':taxVat' => $taxVat,
                ':rgDoc' => $rgDoc,
                ':inscricaoEstadual' => $inscricaoEstadual,
                ':dataNascimento' => $dataNascimento,
                ':telefone' => $telefone,
                ':password' => $password,
                ':idEndereco' => $idEndereco,
                ':idConta' => $idConta
            ])) {
                $this->logger->createLog('Erro ao cadastrar usuário','warning');
                return false;

            }
            $this->logger->createLog('Novo usuário cadastrado');
            return true;
        }

        public function getUser($id)
        {
            $array = [];

            $sql = "SELECT * FROM correntistas WHERE id = :id";
            $stmt = $this->pdo->connect()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $array = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserByTaxVat($taxVat)
        {
            $array = [];

            $sql = "SELECT * FROM correntistas WHERE taxVat = :taxVat";
            $stmt = $this->pdo->connect()->prepare($sql);
            $stmt->execute([':taxVat' => $taxVat]);

             return $array = $stmt->fetch(PDO::FETCH_ASSOC);
        }

    public function updateUser(){
            return "user atualizado";
        }

        public function getAccountBankUser($id)
        {
            $sql = "SELECT numeroConta FROM conta_bancaria C join correntistas C1 on C.idConta = C1.idConta where id = :id;";
            $stmt = $this->pdo->connect()->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $array = $stmt->fetch(PDO::FETCH_ASSOC);
        }

    }

