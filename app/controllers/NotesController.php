<?php

namespace app\controllers;

use app\models\Filters;
use app\models\QueryBuilder;
use app\supports\Validate;
use stdClass;

class NotesController
{
    private stdClass $jwt;
    private ?int $idUser = null;

    public function setDataJWT(stdClass $jwt)
    {
        $this->jwt = $jwt;
        $this->idUser = intval($jwt->data->id_user);
    }

    public function notes()
    {
        $query = new QueryBuilder();
        $filter = new Filters();
        $filter->where('id_user_notes', '=', $this->idUser);
        $notes = $query->setFilters($filter)->setTable("notes")->selectAll();
        $filter->reset()->where('id_user', '=', $this->idUser);
        $user = $query->reset()->setFields('id_user, name_user')->setFilters($filter)->setTable('users')->findBy();
        $data = [
            "user" => $user,
            "notes" => $notes,

        ];
        echo json_encode(['data' => $data]);
        exit;
    }

    public function store()
    {

        $data = [
            'success' => false,
            'msg' => ''
        ];
        $validate = new Validate();
        $validations  = $validate->validations([
            'title_notes' => "required|htmlentities",
            'body_notes' => "htmlentities"
        ]);

        if (!$validations['validations']) {
            $data['msg'] = $validations['msgError'];
            echo json_encode($data);
            http_response_code(400);
            exit;
        }

        $query = new QueryBuilder();
        $dataCreate = [
            'id_user_notes' => $this->idUser,
            'title_notes' => $validations['data']['title_notes'],
            'body_notes' => $validations['data']['body_notes'],
        ];
        $insert = $query->setTable('notes')->create($dataCreate);
        if ($insert) {
            $data['success'] = true;
            $data['msg'] = "Criado com sucesso";
            echo json_encode($data);
            http_response_code(200);
            exit;
        }
    }
    public function show($idNotes)
    {
        $idNotes = intval($idNotes['idNotes']);
        $filter = new Filters();
        $query = new QueryBuilder();
        $filter->where("id_notes", '=', $idNotes, "AND")->where("id_user_notes", '=', $this->idUser);
        $note = $query->setFields('title_notes, body_notes')->setTable('notes')->setFilters($filter)->findBy();
        echo json_encode(["data" => $note]);
        html_entity_decode(200);
        exit;
    }

    public function update($idNotes)
    {
        $idNotes = intval($idNotes['idNotes']);
        $data = [
            'success' => false,
            'msg' => ''
        ];
        $validate = new Validate();
        $validations  = $validate->validations([
            'title_notes' => "required|htmlentities",
            'body_notes' => "htmlentities"
        ]);

        if (!$validations['validations']) {
            $data['msg'] = $validations['msgError'];
            echo json_encode($data);
            http_response_code(400);
            exit;
        }

        $query = new QueryBuilder();
        $filter = new Filters();
        $filter->where("id_user_notes", '=', $this->idUser, "AND")->where("id_notes", "=", $idNotes);
        $dataUpdate = [
            'title_notes' => $validations['data']['title_notes'],
            'body_notes' => $validations['data']['body_notes'],
        ];
        $update = $query->setFilters($filter)->setTable('notes')->update($dataUpdate);
        if (!$update) {
            $data['msg'] = "Erro ao atualizar";
            echo json_encode($data);
            http_response_code(400);
            exit;
        }
        $data['success'] = true;
        $data['msg'] = "Editado com sucesso";
        echo json_encode($data);
        http_response_code(200);
        exit;
    }
    public function destroy($idNotes)
    {
        $data = [
            'success' => false,
            'msg' => ''
        ];
        $idNotes = intval($idNotes['idNotes']);
        $filter = new Filters();
        $query = new QueryBuilder();
        $filter->where("id_notes", '=', $idNotes, "AND")->where("id_user_notes", '=', $this->idUser);
        $delete = $query->setTable('notes')->setFilters($filter)->delete();
        if (!$delete) {
            $data['msg'] = "Erro ao deletar";
            http_response_code(400);
            exit;
        }
        $data['success'] = true;
        $data['msg'] = "Deletado com sucesso";
        echo json_encode($data);
        http_response_code(200);
        exit;
    }
}
