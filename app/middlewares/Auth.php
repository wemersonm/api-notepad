<?php

namespace app\middlewares;


use app\interfaces\MiddlewareInterface;
use app\supports\JWT;

class Auth implements MiddlewareInterface
{
    public function execute()
    {
        return JWT::validateJwt();
    }
}
