<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Modelo {

    private $modelo_id;
    private $nome;
    private $fabricante_id;

    private function bundle($row) {
        $u = new Modelo();
        $u->setModelo_id($row['modelo_id']);
        $u->setNome($row['nome']);
        $u->setFabricante_id($row['fabricante_id']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from modelo order by nome";
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
        $sql = "select * from modelo where modelo_id = ? union all select * from modelo where modelo_id <> ? order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($id, $id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    static function getByFabricante($fabricante_id) {
        $sql = "select * from modelo where fabricante_id = ? order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($fabricante_id));
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }

    static function getById($modelo_id) {
        $sql = "select * from modelo where modelo_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($modelo_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_modelo, $fabricante_id) {
        $sql = "INSERT INTO modelo (nome,fabricante_id) VALUES (?,?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_modelo)), $fabricante_id));
        
        return $stmt->errorInfo();
    }

    static function delete($modelo_id) {
        $sql = 'DELETE FROM modelo WHERE modelo_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($modelo_id));
        return $stmt->errorInfo();
    }

    function getModelo_id() {
        return $this->modelo_id;
    }

    function getNome() {
        return $this->nome;
    }

    function setModelo_id($modelo_id) {
        $this->modelo_id = $modelo_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }
    function getFabricante_id() {
      return $this->fabricante_id;
    }

    function setFabricante_id($fabricante_id) {
       $this->fabricante_id = $fabricante_id;
     }
}




