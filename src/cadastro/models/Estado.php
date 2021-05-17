<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Estado {
  private $estado_id; 
  private $nome; 
  
  private function bundle($row) {
    $u = new Estado();
    $u->setEstado_id($row['estado_id']);
    $u->setNome($row['nome']);
    return $u;
  }
  
  static function getAll() {
    $sql = "select * from estado order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
   static function getById($id) {
    $sql = "select * from estado where estado_id = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  static function getAllById($id) {
    $sql = "select * from estado where estado_id = ? union all select * from estado where estado_id <> ? order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id, $id));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function create($estado) {
        $sql = "INSERT INTO estado (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($estado))));
    }
   
  static function delete($estado_id) {
        $sql = 'DELETE FROM estado WHERE estado_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($estado_id));
        return $stmt->errorInfo();
    }
  
  function getEstado_id() {
    return $this->estado_id;
  }

  function getNome() {
    return $this->nome;
  }

  function setEstado_id($estado_id) {
    $this->estado_id = $estado_id;
  }

  function setNome($nome) {
    $this->nome = $nome;
  }

}

