<?php
namespace WBCrypto\Api\Controller;

use Psr\Log\LoggerInterface;
use WBCrypto\Api\Model\UsersModel as UsersModel;
use WBCrypto\Api\Model\ContaBancariaModel as ContaBancariaModel;

use Pecee\Http\Request;
use Pecee\Http\Url;
use Pecee\Http\Response;

class TransactionsController
{
    //private $user;
    private $conta;
    private $contaDestino;
    private $contaOrigem;
    private $valorTransacao;
    private $novoSaldo;
    private $contaBancaria;
    private $tipoTransacao;
    private $request;
    public $logger;

    public function __construct(ContaBancariaModel $contaBancaria, Request $request, UsersModel $user, LoggerInterface $logger)
    {
        $this->conta = $contaBancaria;
        $this->request = $request;
        $this->user = $user;
        $this->logger = $logger;
    }

    public function saqueDeposito()
    {
        $this->user = $this->user->getUserByTaxVat($this->request->getUser());
        $idAccount = $this->user['idConta'];

        $this->contaDestino = $this->conta->getAccountById($idAccount)['numeroConta'];

        $this->tipoTransacao = $this->request->getInputHandler()->value('TipoTransacao');
        $this->valorTransacao = $this->request->getInputHandler()->value('Valor');

        if($this->valorTransacao && $this->contaDestino && $this->tipoTransacao){

            $this->contaBancaria = $this->conta->getAccountByNumber($this->contaDestino);

            $messageLog = 'Transação ' . $this->tipoTransacao . ' realizada com sucesso. Valor: R$ ' . $this->valorTransacao;

            switch ($this->tipoTransacao){
                case 'saque';
                    if($this->contaBancaria['saldoAtual'] < $this->valorTransacao){
                        $result['Error'] =  "Você não tem saldo o suficiente para realizar a operação!";
                    }else {
                        $this->novoSaldo = $this->contaBancaria['saldoAtual'] - $this->valorTransacao;
                        $this->conta->setSaldoAtual($this->novoSaldo);
                        $this->conta->setNumeroConta($this->contaBancaria['numeroConta']);

                        $this->logger->info($messageLog);

                        $result['Success'] =  "Operação realizada com sucesso!";
                    }
                    break;
                case 'deposito';
                    $this->novoSaldo = $this->contaBancaria['saldoAtual'] + $this->valorTransacao;
                    $this->conta->setSaldoAtual($this->novoSaldo);
                    $this->conta->setNumeroConta($this->contaBancaria['numeroConta']);
                    $this->logger->info($messageLog);
                    $result['Success'] =  "Operação realizada com sucesso!";
                    break;
            }
            try{
                $this->conta->updateAccount($this->conta->getNumeroConta(), $this->conta->getSaldoAtual());
            } catch(\Exception $e){
                $result['Error'] =  "Falha na operação." . $e->getMessage();
            }
        }else {
            $result['Error'] =  "Informe todos os campos para transação";
        }
        echo json_encode($result);
    }

    public function transferir()
    {
        $this->user = $this->user->getUserByTaxVat($this->request->getUser());
        $idAccount = $this->user['idConta'];

        $this->contaOrigem = $this->conta->getAccountById($idAccount)['numeroConta'];

        $this->contaDestino = $this->request->getInputHandler()->value('ContaDestino');
        $this->valorTransacao = $this->request->getInputHandler()->value('Valor');

        if(!$this->conta->getAccountByNumber($this->contaDestino)){
            $result['Error'] = 'Conta destino não encontrada!';
            return json_encode($result);

            die();
        }

        if($this->valorTransacao && $this->contaOrigem && $this->contaDestino){

            try{

                $this->contaBancaria = $this->conta->getAccountByNumber($this->contaOrigem);

                if($this->contaBancaria['saldoAtual'] < $this->valorTransacao){
                    $messageLog = "Falha na transferência. Saldo insuficiente! Seu Saldo é de R$" . $this->contaBancaria['saldoAtual'] . " e o valor solicitado na transferência foi de R$" . $this->valorTransacao;
                    $this->logger->info($messageLog);

                    $result['Error'] = $messageLog;
                }else {
                    //Executa o Débito na conta origem
                    $this->novoSaldo = $this->contaBancaria['saldoAtual'] - $this->valorTransacao;
                    $this->conta->setSaldoAtual($this->novoSaldo);
                    $this->conta->setNumeroConta($this->contaBancaria['numeroConta']);
                    $this->conta->updateAccount($this->conta->getNumeroConta(), $this->conta->getSaldoAtual());

                    //Executa o Crédito na conta destino
                    $this->contaBancaria = $this->conta->getAccountByNumber($this->contaDestino);
                    $this->novoSaldo = $this->contaBancaria['saldoAtual'] + $this->valorTransacao;
                    $this->conta->setSaldoAtual($this->novoSaldo);
                    $this->conta->setNumeroConta($this->contaBancaria['numeroConta']);
                    $this->conta->updateAccount($this->conta->getNumeroConta(), $this->conta->getSaldoAtual());

                    $messageLog = "Nova Transferência Realizada da conta " . $this->contaOrigem . " para a conta " . $this->contaDestino . " no valor de R$" . $this->valorTransacao;
                    $this->logger->info($messageLog);

                    $result['Success'] =  $messageLog;
                }

            }catch (\Exception $e){
                $messageLog = "Não foi possível realizar a transferência: " . $e->getMessage();

                $this->logger->info($messageLog, 'info');

                $result['Error'] =  $messageLog;
            }
        }
        echo json_encode($result);
    }
}