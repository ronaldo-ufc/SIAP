<?php
namespace siap\setor\models;
use siap\models\DBSiap;

class Setor{
  private $setor_id;
  private $nome;
  private $ativo;
  private $sigla;
  private $responsavel;
  
  
  private function bundle($row){
    $u = new Setor();
    $u->setSetor_id($row['setor_id']);
    $u->setNome($row['nome']);
    $u->setAtivo($row['ativo']);
    $u->setSigla($row['sigla']);
      
    return $u;
  }
  
  static function getAll(){
    $sql = "select * from setor order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getAllById($id){
    $sql = "select * from setor where setor_id = ?  union all  select * from setor  where setor_id <> ? order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id, $id));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getById($setor_id){
    $sql = "select * from setor where setor_id = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor_id));
    $row = $stmt->fetch();
    if ($row == null){
      return null;
    }
    return self::bundle($row);
  }
  
  static function create($nome_setor, $sigla){
    $sql = "INSERT INTO setor (nome, sigla) VALUES (?, ?)";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(strtoupper(tirarAcentos($nome_setor)), strtoupper($sigla)));
  }
  
           
  function getSetor_id() {
    return $this->setor_id;
  }

          
  function setSetor_id($setor_id) {
    $this->setor_id = $setor_id;
  }

  function getNome() {
    return $this->nome;
  }

  function setNome($nome) {
    $this->nome = $nome;
  }
  function getAtivo() {
    return $this->ativo;
  }

  function setAtivo($ativo) {
    $this->ativo = $ativo;
  }

 
  function getSigla() {
    return $this->sigla;
  }

  function setSigla($sigla) {
    $this->sigla = $sigla;
  }
  public function getResponsavel() {
    return $this->responsavel;
  }

  public function setResponsavel($responsavel) {
    $this->responsavel = $responsavel;
  }



}

