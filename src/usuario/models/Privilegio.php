<?php

namespace siap\usuario\models;

use siap\models\DBSiap;

class Privilegio {
  private $privilegio_codigo;
  private $privilegio_nome;
  
  private function Bundle($row){
    $u = new Privilegio();
    $u->setPrivilegio_codigo($row['privilegio_codigo']);
    $u->setPrivilegio_nome($row['privilegio_nome']);
    return $u;
  }
  
  static function getByCodigo($codigo){
    $sql = "SELECT * FROM siap.privilegio WHERE privilegio_codigo = ?";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($codigo));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  public static function getAll() {
    $sql = "SELECT * FROM siap.privilegio";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
          
  function getPrivilegio_codigo() {
    return $this->privilegio_codigo;
  }

  function getPrivilegio_nome() {
    return $this->privilegio_nome;
  }

  function setPrivilegio_codigo($privilegio_codigo) {
    $this->privilegio_codigo = $privilegio_codigo;
  }

  function setPrivilegio_nome($privilegio_nome) {
    $this->privilegio_nome = $privilegio_nome;
  }


}
