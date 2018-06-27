<?php
namespace siap\setor\models;
use siap\models\DBSiap;
use siap\setor\models\Setor;
use siap\usuario\models\Usuario;

class SetorResponsavel{
  private $setor_id;
  private $responsavel_id;
  private $data_inicio;
  private $data_fim;
  private static $setor = null;
  private static $responsavel = null;
  
  private function bundle($row){
    $u = new SetorResponsavel();
    $u->setSetor_id($row['setor_id']);
    $u->setResponsavel_id($row['responsavel_id']);
    $u->setData_inicio($row['data_inicio']);
    $u->setData_fim($row['data_fim']);
    $u->setResponsavel($row['responsavel_id']);
    $u->setSetor($row['setor_id']);
    return $u;
  }
  
  static function getAll(){
    $sql = "select * from setor_responsavel where data_fim >= now() ";
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
    $sql = "select * from setor_responsavel where setor_id = ? order by data_fim desc limit 1 ";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }

  static function create($setor, $responsavel, $inicio, $fim){
    $sql = 'INSERT INTO setor_responsavel (setor_id, responsavel_id, data_inicio, data_fim) VALUES (?, ?, ?, ?)';
    
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
  function getSetor_id() {
    return $this->setor_id;
  }

  function getResponsavel_id() {
    return $this->responsavel_id;
  }

  function getData_inicio() {
    return $this->data_inicio;
  }

  function getData_fim() {
    return $this->data_fim;
  }

  function getSetor() {
    return $this->setor;
  }

  function getResponsavel() {
   
    return $this->responsavel;
  }

  function setSetor_id($setor_id) {
    $this->setor_id = $setor_id;
  }

  function setResponsavel_id($responsavel_id) {
    $this->responsavel_id = $responsavel_id;
  }

  function setData_inicio($data_inicio) {
    $this->data_inicio = $data_inicio;
  }

  function setData_fim($data_fim) {
    $this->data_fim = $data_fim;
  }

  function setSetor($setor) {
    if ($this->setor == null) {
      $this->setor = Setor::getById($setor);
    }
  }

  function setResponsavel($responsavel) {
    if ($this->responsavel == null){
      $this->responsavel = Usuario::getByLogin($responsavel);
    }
    
  }

}

