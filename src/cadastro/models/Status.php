<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Status {
  private $status_id; 
  private $nome; 
  
  private function bundle($row) {
    $u = new Status();
    $u->setStatus_id($row['status_id']);
    $u->setNome($row['nome']);
    return $u;
  }
  
  static function getAll() {
    $sql = "select * from status order by nome";
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
    $sql = "select * from status where status_id = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  static function getAllById($id) {
    $sql = "select * from status where status_id = ? union all select * from status where status_id <> ? order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id, $id));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function create($status) {
        $sql = "INSERT INTO status (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($status))));
    }
   
  static function delete($status_id) {
        $sql = 'DELETE FROM status WHERE status_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($status_id));
        return $stmt->errorInfo();
    }
  
  function getStatus_id() {
    return $this->status_id;
  }

  function getNome() {
    return $this->nome;
  }

  function setStatus_id($status_id) {
    $this->status_id = $status_id;
  }

  function setNome($nome) {
    $this->nome = $nome;
  }

}

