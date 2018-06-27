<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Aquisicao {

    private $aquisicao_id;
    private $nome;

    private function bundle($row) {
        $u = new Aquisicao();
        $u->setAquisicao_id($row['aquisicao_id']);
        $u->setNome($row['nome']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from aquisicao order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getById($aquisicao_id) {
        $sql = "select * from aquisicao where aquisicao_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($aquisicao_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_aquisicao) {
        $sql = "INSERT INTO aquisicao (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_aquisicao))));
        
        return $stmt->errorInfo();
    }

    static function delete($aquisicao_id) {
        $sql = 'DELETE FROM aquisicao WHERE aquisicao_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($aquisicao_id));
        return $stmt->errorInfo();
    }

    function getAquisicao_id() {
        return $this->aquisicao_id;
    }

    function getNome() {
        return $this->nome;
    }

    function setAquisicao_id($aquisicao_id) {
        $this->aquisicao_id = $aquisicao_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

}
