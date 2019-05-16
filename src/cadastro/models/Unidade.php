<?php
namespace siap\cadastro\models;
use siap\models\DBSiap;

class Unidade{
  private $unidade_codigo; 
  private $nome; 
  private $valor;
  
  private function bundle($row){
    $u = new Unidade();
    $u->setUnidade_codigo($row['unidade_codigo']);
    $u->setNome($row['nome']);
    $u->setValor($row['valor']);
    return $u;
  }
  static function create($nome) {
    $sql = "INSERT INTO unidade (nome) VALUES (?)";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(strtoupper(tirarAcentos($nome))));
  }
  static function getById($unidade_id) {
    $sql = "SELECT * FROM unidade where unidade_codigo = ?";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($unidade_id));
    $row = $stmt->fetch();
    if ($row == null) {
        return FALSE;
    }
    return self::bundle($row);
  }
  
    static function getAllById($id) {
    $sql = "select * from unidade order by unidade_codigo = ? desc, nome asc";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
    }
  
  static function getAll() {
    $sql = "select * from unidade order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
    }
  
  public function getUnidade_codigo() {
    return $this->unidade_codigo;
  }

  public function getNome() {
    return $this->nome;
  }

  public function getValor() {
    return $this->valor;
  }

  public function setUnidade_codigo($unidade_codigo) {
    $this->unidade_codigo = $unidade_codigo;
  }

  public function setNome($nome) {
    $this->nome = $nome;
  }

  public function setValor($valor) {
    $this->valor = $valor;
  }
}

