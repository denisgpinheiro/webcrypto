<?php
namespace WBCrypto\Api\Controller;

use WBCrypto\Api\Model\UsersModel as UsersModel;
use Pecee\Http\Request;
use Pecee\Http\Url;
use Pecee\Http\Response;


class GetUserController
{
    private $user;
    private $idUser;
    private $request;

    public function __construct()
    {
        $this->user = new UsersModel();
    }

    public function getUserArray()
    {
        $this->request = new Request();
        $this->idUser = $this->request->getUrl()->value('id');

        $result[] = $this->user->getUser($this->idUser);


        echo json_encode($result);
    }
}