<?php
namespace WBCrypto\Api\Controller;

use WBCrypto\Api\Model\UsersModel as UsersModel;
use Pecee\Http\Request;
use Pecee\Http\Url;
use Pecee\Http\Response;

class ListUsersController
{
    private $users;
    private $request;

    public function __construct(UsersModel $users)
    {
        $this->users = $users;
    }

    public function listUsersArray()
    {
        $result[] = $this->users->getAllUsers();
        echo json_encode($result);

    }
}