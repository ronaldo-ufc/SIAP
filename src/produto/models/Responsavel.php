<?php

namespace siap\produto\models;

use siap\models\DBSiap;

class Responsavel {

    private $responsavel_id;
    private $nome;

    private function bundle($row) {
        $u = new Responsavel();
        $u->setResponsavel_id($row['responsavel_id']);
        $u->setNome($row['nome']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from responsavel order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

//    static function getAllById($id) {
//        $sql = "select * from categoria where categoria_id = ? union all select * from categoria where categoria_id <> ? order by nome";
//        $stmt = DBSiap::getSiap()->prepare($sql);
//        $stmt->execute(array($id, $id));
//        $rows = $stmt->fetchAll();
//        $result = array();
//        foreach ($rows as $row) {
//            array_push($result, self::bundle($row));
//        }
//        return $result;
//    }

    static function getById($responsavel_id) {
        $sql = "select * from siap.responsavel where responsavel_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($responsavel_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_responsavel) {
        $sql = "INSERT INTO siap.responsavel (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_responsavel))));
    }

    static function delete($responsavel_id) {
        $sql = 'DELETE FROM responsavel WHERE responsavel_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($responsavel_id));
        return $stmt->errorInfo();
    }

    public function getResponsavel_id() {
        return $this->responsavel_id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setResponsavel_id($responsavel_id) {
        $this->responsavel_id = $responsavel_id;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

}
