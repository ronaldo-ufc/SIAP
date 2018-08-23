<?php
namespace siap\setor\models;
use siap\models\DBSiap;
use siap\setor\models\Setor;
use siap\usuario\models\Usuario;

class AgenteSetorial{
  private $setor_id;
  private $agente_id;
  private $data_inicio;
  private $data_fim;
  private static $setor;
  private static $agente;
  
  private function bundle($row){
    $u = new AgenteSetorial();
    $u->setSetor_id($row['setor_id']);
    $u->setResponsavel_id($row['agente_id']);
    $u->setData_inicio($row['data_inicio']);
    $u->setData_fim($row['data_fim']);
    
    $u->setAgente(Usuario::getByLogin($row['agente_id']));
    
    $u->setSetor(Setor::getById($row['setor_id']));
    return $u;
  }
  
  static function getAll(){
    $sql = "select * from setor_agente where data_fim >= current_date ";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  static function getLastBySetor($setor){
    $sql = "select * from setor_agente where setor_id = ? order by data_fim desc limit 1 ";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }

  static function create($setor, $responsavel, $inicio, $fim){
    $sql = 'INSERT INTO setor_agente (setor_id, responsavel_id, data_inicio, data_fim) VALUES (?, ?, ?, ?)';
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor, $responsavel, $inicio, $fim));
    return $stmt->errorInfo();
  }
  static function delete($setor, $responsavel, $inicio){
    $sql = 'DELETE FROM setor_responsavel WHERE setor_id = ? and responsavel_id = ? and data_inicio = ?';
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor, $responsavel, $inicio));
    return $stmt->errorInfo();
  }
  
  static function getResponsavelBySetorAndData($setor, $data){
    $sql = "select * from setor_responsavel where setor_id = ? and (? <= data_fim and ? >= data_inicio)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor, $data, $data));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  static function getCurrentResponsavelBySetor($setor){
    $sql = "select * from setor_responsavel where setor_id = ? and (now() <= data_fim and now() > data_inicio )";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  
  

}

