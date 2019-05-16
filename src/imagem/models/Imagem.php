<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace siap\imagem\models;
use siap\models\DBSiap;
/**
 * Description of Imagem
 *
 * @author Admin
 */
class Imagem {
  private $codigo;
  private $nome;
  private $caminho;
  
  private function bundle($row){
    $u = new Imagem();
    $u->setCodigo($row['codigo']);
    $u->setNome($row['nome']);
    $u->setCaminho($row['caminho']);
    return $u;
  }

static function getAll(){

    $sql = "select * from imagem ";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();

    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function create($nome, $caminho){
    $sql = "INSERT INTO imagem (nome, caminho) VALUES (?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($nome, $caminho));
    return $stmt->errorInfo();
  }
  
  public function getCodigo() {
    return $this->codigo;
  }

  public function getNome() {
    return $this->nome;
  }

  public function getCaminho() {
    return $this->caminho;
  }

  public function setCodigo($codigo) {
    $this->codigo = $codigo;
  }

  public function setNome($nome) {
    $this->nome = $nome;
  }

  public function setCaminho($caminho) {
    $this->caminho = $caminho;
  }


}
