<?php
use Pecee\SimpleRouter\SimpleRouter;

use WBCrypto\Api\Controller\CreateUserController as CreateUser;
use WBCrypto\Api\Controller\ListUsersController as ListUsers;
use WBCrypto\Api\Controller\GetUserController as GetUser;
use WBCrypto\Api\Controller\TransactionsController as Transacoes;
use WBCrypto\Api\Auth\AuthUserController as Auth;
use WBCrypto\Middlewares\AuthMiddleware as Middleware;
use DI\Factory as AppFactory;

//use WBCrypto\src\MyClassLoader as MyClassLoader;

SimpleRouter::setCustomClassLoader(new \WBCrypto\MyClassLoader());

    SimpleRouter::get('/', function() {
        return 'Hello world';
    });

    SimpleRouter::group(['middleware' => Middleware::class], function (){
        SimpleRouter::post('/WBCrypto/Api/transacaoPessoal/', [Transacoes::class, 'saqueDeposito']);
        SimpleRouter::post('/WBCrypto/Api/transacaoExterna/', [Transacoes::class, 'transferir']);
    });

    SimpleRouter::get('/WBCrypto/Api/allUsers', [ListUsers::class, 'listUsersArray']);
    SimpleRouter::post('/WBCrypto/Api/newUser', [CreateUser::class, 'createNewUser']);
    SimpleRouter::get('/WBCrypto/Api/getUser/{id}', [GetUser::class, 'getUserArray']);


    SimpleRouter::post('/WBCrypto/Api/TesteAuth/', [Auth::class, 'userLoginValidate']);

    try{
        SimpleRouter::start();
    }catch (Exception $e){
        var_dump($e);
    }

