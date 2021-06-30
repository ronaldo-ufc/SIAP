<?php

namespace siap\setor\models;

use siap\models\DBSiap;

class Bloco {

    private $bloco_id;
    private $nome;

    private function bundle($row) {
        $u = new Bloco();
        $u->setBloco_id($row['bloco_id']);
        $u->setNome($row['nome']);

        return $u;
    }

    static function getAll($bloco_id = 1) {
        $sql = "select * from bloco b order by b.bloco_id = ? desc, nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($bloco_id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    static function getById($bloco_id) {
        $sql = "select * from bloco where bloco_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($bloco_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    public function getBloco_id() {
        return $this->bloco_id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setBloco_id($bloco_id) {
        $this->bloco_id = $bloco_id;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

}
