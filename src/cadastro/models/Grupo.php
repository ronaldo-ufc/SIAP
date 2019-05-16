<?php

namespace siap\cadastro\models;
use siap\models\DBSiap;

class Grupo {

    private $grupo_codigo;
    private $nome;

    private function bundle($row) {
        $u = new Grupo();
        $u->setGrupo_codigo($row['grupo_codigo']);
        $u->setNome($row['nome']);
        return $u;
    }
    static function getAll() {
        $sql = "select * from grupo order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
     static function getAllById($id) {
        $sql = "select * from grupo order by grupo_codigo = ? desc, nome asc";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    
    static function getById($grupo_codigo) {
        $sql = "select * from grupo where grupo_codigo = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($grupo_codigo));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_grupo) {
        $sql = "INSERT INTO grupo (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_grupo))));
    }

    static function delete($grupo_codigo) {
        $sql = 'DELETE FROM grupo WHERE grupo_codigo = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($grupo_codigo));
        return $stmt->errorInfo();
    }

    function getGrupo_codigo() {
        return $this->grupo_codigo;
    }

    function getNome() {
        return $this->nome;
    }

    function setGrupo_codigo($grupo_codigo) {
        $this->grupo_codigo = $grupo_codigo;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

}
