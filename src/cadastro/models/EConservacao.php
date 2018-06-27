<?php

namespace siap\cadastro\models;
use siap\models\DBSiap;

class EConservacao {
  private $conservacao_id; 
  private $nome; 
  
  private function bundle($row) {
    $u = new EConservacao();
    $u->setConservacao_id($row['conservacao_id']);
    $u->setNome($row['nome']);
    return $u;
  }
  
  static function getAll() {
    $sql = "select * from conservacao order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function create($conservacao) {
        $sql = "INSERT INTO conservacao (nome) VALUES (?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($conservacao))));
    }
   
  static function delete($conservacao_id) {
        $sql = 'DELETE FROM conservacao WHERE conservacao_id = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($conservacao_id));
        return $stmt->errorInfo();
    }
  
    
  function getConservacao_id() {
    return $this->conservacao_id;
  }

  function getNome() {
    return $this->nome;
  }

  function setConservacao_id($conservacao_id) {
    $this->conservacao_id = $conservacao_id;
  }

  function setNome($nome) {
    $this->nome = $nome;
  }
}

