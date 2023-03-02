<?php
namespace WBCrypto\DI;

use Monolog\Handler\HandlerWrapper;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use WBCrypto\Config\ConnectDatabase;
use WBCrypto\Interfaces\DatabaseInterface;

use function DI\Factory;

    return [
        PDO::class => factory(function (){
            return new \PDO("mysql:host=localhost;dbname=academiaWebjump", "root", "db123");
        }),
        DatabaseInterface::class => factory(function (ContainerInterface $container){
            return new ConnectDatabase($container->get(\PDO::class));

        }),
        LoggerInterface::class => factory(function (ContainerInterface $container){
            $logger = new Logger('webcrypto-logs');
            $logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/Api/Logs/system.log'));
            return $logger;
        })
    ];