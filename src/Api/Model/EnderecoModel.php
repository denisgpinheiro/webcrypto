<?php
namespace WBCrypto\Api\Model;

use WBCrypto\Config\ConnectDatabase;
use PDO;

class EnderecoModel
{
    public $idEndereco;
    public $rua;
    public $bairro;
    public $cidade;
    public $estado;
    public $cep;
    public $numero;
    public $pdo;

    public function __construct(){
        $this->pdo = new ConnectDatabase();
    }

    public function getIdEndereco(){
        return $this->idEndereco;
    }

    public function getRua(){
        return $this->rua;
    }
    public function setRua($rua)
    {
        $this->rua = $rua;
    }

    public function getBairro(){
        return $this->bairro;
    }
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    public function getCidade(){
        return $this->cidade;
    }
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
    }

    public function getEstado(){
        return $this->estado;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getCep(){
        return $this->cep;
    }
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    public function getNumero(){
        return $this->numero;
    }
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }


    public function getAllAddress(){

        $array = [];

        $sql = "SELECT * FROM endereco ORDER BY idEndereco";
        $stmt = $this->pdo->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        if($stmt) {
            foreach ($stmt as $data) {
                $array[] = $data;
            }
        }else{
            $array['error'][] = "Nenhuma conta encontrada";
        }
        return $array;
    }

    public function addAddress(
        $rua,
        $bairro,
        $cidade,
        $estado,
        $cep,
        $numero){
        $sql = "INSERT INTO endereco (
                rua,
                bairro, 
                cidade,
                estado,
                cep,
                numero) VALUES (
                :rua,
                :bairro, 
                :cidade,
                :estado,
                :cep,
                :numero)"
        ;

        $stmt = $this->pdo->connect()->prepare($sql);

        if (!$stmt->execute([
            ':rua' => $rua,
            ':bairro' => $bairro,
            ':cidade' => $cidade,
            ':estado' => $estado,
            ':cep' => $cep,
            ':numero' => $numero
        ])) {
            return false;
        }
        $sql = "SELECT last_insert_id() as id";
        $stmt = $this->pdo->connect()->prepare($sql);
        $queryResult = $stmt->execute();

        if ($queryResult !== false){
           $result = $stmt->fetch();

           $this->idEndereco = $result['id'];
        }
        return true;
    }

    public function getAddress($idEndereco)
    {
        $array = [];

        $sql = "SELECT * FROM endereco WHERE idEndereco = :idEndereco";
        $stmt = $this->pdo->connect()->prepare($sql);
        $stmt->execute([':idEndereco' => idEndereco]);
        return $array = $stmt->fetch();
    }

    public function updateAccount(){
        return "user atualizado";
    }

}