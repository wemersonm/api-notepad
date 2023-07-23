<?php

namespace app\supports;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as JWT_;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JWT
{
    public static function validateJwt()
    {
        try {
            if (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION'])) {
                http_response_code(401);
                echo json_encode(['error' => "Usuario não autenticado (auth)"]);
                exit;
            }
            $bearer = $_SERVER['HTTP_AUTHORIZATION'];
            $token = str_replace("Bearer ", "", $bearer);
            $decoded = JWT_::decode($token, new Key($_ENV['KEYJWT'], 'HS256'));
            return $decoded;
        } catch (ExpiredException $e) {
            http_response_code(401);
            echo "Token expired";
            exit;
        } catch (SignatureInvalidException $e) {
            http_response_code(401);
            echo "Token invalid";
            exit;
        } catch (Exception $e) {
            http_response_code(401);
            echo "Error decoding token";
            exit;
        }
    }
}
