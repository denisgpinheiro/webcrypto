<?php
namespace WBCrypto\Api\Controller;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerController
{
    public function createLog($mensagem, $modo = 'info'){

        $logger = new Logger('webcrypto-logs');
        $logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/Logs/system.log'));

        switch($modo){

            case 'warning':
                $logger->warning($mensagem);
                break;
            case 'error':
                $logger->error($mensagem);
                break;
            case 'debug':
                $logger->debug($mensagem);
                break;
            case 'notice':
                $logger->notice($mensagem);
                break;
            case 'critical':
                $logger->critical($mensagem);
                break;
            case 'alert':
                $logger->alert($mensagem);
                break;
            case 'emergency':
                $logger->emergency($mensagem);
                break;

            default:
                $logger->info($mensagem);
                break;
        }
    }
}