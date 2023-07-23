<?php

namespace app\controllers;

use app\models\Filters;
use app\models\QueryBuilder;
use app\supports\Request;
use app\supports\Validate;
use Firebase\JWT\JWT;

class AuthController
{
    public function login()
    {

        $data = [
            'success' => false,
            'msg' => ''
        ];
        $validate = new Validate();
        $validations = $validate->validations([
            'email_user' => 'required|email',
            'password_user' => 'required'
        ]);
        if (!$validations['validations']) {
            $data['msg'] = "Dados invalidos";
            echo json_encode($data);
            http_response_code(400);
            exit;
        }

        $userExist = $this->findUserBy($validations['data']['email_user']);

        if (empty($userExist)) {

            $data['msg'] = "Usuario não encontrado";
            echo json_encode($data);
            http_response_code(404);
            exit;
        }

        if (!password_verify($validations['data']['password_user'], $userExist['password_user'])) {
            $data['msg'] = "Email e/ou senha incorretos";
            echo json_encode($data);
            http_response_code(401);
            exit;
        }

        $enconde = $this->generateJWT($userExist);
        $data = [
            'success' => true,
            'msg' => 'Email e senha Corretos',
            'data' => $enconde
        ];
        echo json_encode($data);
        http_response_code(200);
        exit;
    }

    public function store()
    {
        $data = [
            'success' => false,
            'msg' => ''
        ];
        $validate = new Validate();
        $validations = $validate->validations([
            'name_user' => "required|alpha",
            'email_user' => "required|email|unique:users",
            'password_user' => "required|min:8",
            'password_repeat' => "required|same:password_user"
        ]);


        if (!$validations['validations']) {
            $data['msg'] = "Dados invalidos";
            echo json_encode($data);
            http_response_code(400);
            exit;
        }
        $created = $this->createUser($validations);
        if (!$created) {
            $data['msg'] = "Falha ao registrar";
            echo json_encode($data);
            http_response_code(422);
            exit;
        }
        $data = [
            'success' => true,
            'msg' => 'Usuario criado com sucesso'
        ];
        echo json_encode($data);
        http_response_code(200);
        exit;
    }
    
    private function findUserBy(string $value)
    {
        $filter = new Filters();
        $query = new QueryBuilder();
        $filter->where('email_user', '=', $value);
        $userExist = $query->setFilters($filter)->setTable('users')->findBy();
        return $userExist;
    }

    private function generateJWT($userExist)
    {
        $payload = [
            'exp' => strtotime("+50 hours", time()),
            'iat' => time(),
            'data' => [
                'id_user' => $userExist['id_user'], 'name_user' => $userExist['name_user'],
                'email_user' => $userExist['email_user']
            ]
        ];
        $enconde = JWT::encode($payload, $_ENV['KEYJWT'], 'HS256');
        return $enconde;
    }
    private function createUser(array $validations)
    {
        $query = new QueryBuilder();
        $validations['data']['password_user'] = password_hash($validations['data']['password_user'], PASSWORD_DEFAULT);
        unset($validations['data']['password_repeat']);
        $created = $query->setTable("users")->create($validations['data']);
        return $created;
    }
}
