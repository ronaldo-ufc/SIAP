<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Fabricante {

    private $fabricante_id;
    private $nome;

    private function bundle($row) {
        $u = new Fabricante();
        $u->setFabricante_id($row['fabricante_id']);
        $u->setNome($row['nome']);
        return $u;
    }

    static function getAll() {
      $sql = "select * from fabricante order by nome";
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
      $sql = "select * from fabricante where fabricante_id = ? union all select * from fabricante where fabricante_id <> ? order by nome";
      $stmt = DBSiap::getSiap()->prepare($sql);
      $stmt->execute(array($id, $id));
      $rows = $stmt->fetchAll();
      $result = array();
      foreach ($rows as $row) {
          array_push($result, self::bundle($row));
      }
      return $result;
    }
   
    static function getById($fabricante_id) {
        $sql = "select * from fabricante where fabricante_id = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($fabricante_id));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }
    
    static function getByNome($str) {
        $sql = "select * from fabricante where nome LIKE '%?%' LIMIT 100";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($str));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome_fabricante) {
        $sql = "INSERT INTO fabricante (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome_fabricante))));
    }

    static function delete($categoria_id) {
        $sql = 'DELETE FROM fabricante WHERE fabricante_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($categoria_id));
        return $stmt->errorInfo();
    }

    function getFabricante_id() {
        return $this->fabricante_id;
    }

    function getNome() {
        return $this->nome;
    }

    function setFabricante_id($fabricante_id) {
        $this->fabricante_id = $fabricante_id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

}
