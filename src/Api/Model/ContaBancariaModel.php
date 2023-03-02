<?php
namespace WBCrypto\Api\Model;

use Psr\Log\LoggerInterface;
use WBCrypto\Config\ConnectDatabase;
use PDO;

class ContaBancariaModel
{
    public $idConta;
    public $numeroConta;
    public $saldoAtual;
    public $saldoAnterior;
    public $pdo;
    public $logger;
    public $contaDestino;

    public function __construct(ConnectDatabase $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function getIdConta()
    {
        return $this->idConta;
    }

    public function getNumeroConta()
    {
        return $this->numeroConta;
    }

    public function setNumeroConta($numeroConta)
    {
        $this->numeroConta = $numeroConta;
    }

    public function getSaldoAtual()
    {
        return $this->saldoAtual;
    }

    public function setSaldoAtual($saldoAtual)
    {
        $this->saldoAtual = $saldoAtual;
    }

    public function getSaldoAnterior()
    {
        return $this->saldoAnterior;
    }

    public function setSaldoAnterior($saldoAnterior)
    {
        $this->saldoAnterior = $saldoAnterior;
    }


    public function getAllAccounts()
    {
        $array = [];

        $sql = "SELECT * FROM conta_bancaria ORDER BY idConta";
        $stmt = $this->pdo->connect()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt) {
            foreach ($stmt as $data) {
                $array[] = $data;
            }
        } else {
            $array['error'][] = "Nenhuma conta encontrada";
        }
        return $array;
    }

    public function newAccount(
        $saldoAtual,
        $saldoAnterior)
    {

        $numeroConta = rand(1, 9999);

        $sql = "INSERT INTO conta_bancaria (
                numeroConta,
                saldoAtual, 
                saldoAnterior) VALUES (
                :numeroConta,
                :saldoAtual, 
                :saldoAnterior)";

        $stmt = $this->pdo->connect()->prepare($sql);

        if (!$stmt->execute([
            ':numeroConta' => $numeroConta,
            ':saldoAtual' => $saldoAtual,
            ':saldoAnterior' => $saldoAnterior
        ])) {
            $this->logger->warning('Erro ao criar conta: ' . $this->pdo->errorCode());
            return false;
        }

        $sql = "SELECT last_insert_id() as id";
        $stmt = $this->pdo->connect()->prepare($sql);
        $queryResult = $stmt->execute();

        if ($queryResult !== false) {
            $result = $stmt->fetch();
            $this->idConta = $result['id'];

            $this->logger->info('Nova conta criada: {numeroConta}', ['numeroConta'=> $numeroConta]);
        }
        return true;
    }

    public function getAccountById($idConta)
    {
        $array = [];

        $sql = "SELECT * FROM conta_bancaria WHERE idConta = :idConta";
        $stmt = $this->pdo->connect()->prepare($sql);
        $stmt->execute([':idConta' => $idConta]);
        return $array = $stmt->fetch();
    }

    public function getAccountByNumber($numeroConta)
    {
        $array = [];

        $sql = "SELECT * FROM conta_bancaria WHERE numeroConta = :numeroConta";
        $stmt = $this->pdo->connect()->prepare($sql);
        $stmt->execute([':numeroConta' => $numeroConta]);
        return $array = $stmt->fetch();
    }

    public function updateAccount($contaDestino, $saldoAtual)
    {
        if ($contaDestino > 0) {
            $sql = "UPDATE conta_bancaria SET saldoAtual = :saldoAtual WHERE numeroConta = :contaDestino";
        } else {
            return "Conta inexistente";
        }

        $stmt = $this->pdo->connect()->prepare($sql);

        if (!$stmt->execute([
            ':contaDestino' => $contaDestino,
            ':saldoAtual' => $saldoAtual
        ])) {
            return false;
        }
    }
}