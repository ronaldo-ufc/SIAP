<?php
namespace siap\material\models;
use siap\models\DBSiap;
use siap\material\models\Produto;

class RequisicaoItens{
  private $requisicao_codigo; 
  private $produto_codigo; 
  private $quantidade;
  private $quantidade_atendida;
  private $status;
  private $produto;
  
  private function bundle($row){
    $u = new RequisicaoItens();
    $u->setRequisicao_codigo($row['requisicao_codigo']);
    $u->setProduto_codigo($row['produto_codigo']);
    $u->setQuantidade($row['quantidade']);
    $u->setStatus($row['status']);
    $u->setQuantidade_atendida($row['quantidade_atendida']);
    
    #Objetos
    $u->setProduto(Produto::getById($row['produto_codigo']));
    return $u;
  }
  
  static function create($requisicao_codigo, $produto, $quantidade){
    $sql = "INSERT INTO siap.requisicao_item (requisicao_codigo, produto_codigo, quantidade) VALUES (?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao_codigo, $produto, $quantidade));
    return $stmt->errorInfo();
  }
  
  static function delete($produto){
    $sql = "DELETE FROM siap.requisicao_item WHERE produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($produto));
    return $stmt->errorInfo();
  }
  
  static function getByRequisicao($requisicao){
    $sql = "SELECT * FROM siap.requisicao_item where requisicao_codigo = ? order by id desc";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }

  public function getRequisicao_codigo() {
    return $this->requisicao_codigo;
  }

  public function getProduto_codigo() {
    return $this->produto_codigo;
  }

  public function getQuantidade() {
    return $this->quantidade;
  }

  public function getStatus() {
    return $this->status;
  }

  public function setRequisicao_codigo($requisicao_codigo) {
    $this->requisicao_codigo = $requisicao_codigo;
  }

  public function setProduto_codigo($produto_codigo) {
    $this->produto_codigo = $produto_codigo;
  }

  public function setQuantidade($quantidade) {
    $this->quantidade = $quantidade;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getProduto() {
    return $this->produto;
  }

  public function setProduto($produto) {
    $this->produto = $produto;
  }
  public function getQuantidade_atendida() {
    return $this->quantidade_atendida;
  }

  public function setQuantidade_atendida($quantidade_atendida) {
    $this->quantidade_atendida = $quantidade_atendida;
  }

  public function getAtendida(){
//    if($this->status == 'C'){
//      return $this->quantidade;
//    }
    return $this->quantidade_atendida;
  }

  public function getStatusNome(){
    switch ($this->status){
      case 'C': return 'Cadastrado';
      case 'A': return 'Aprovado';
      case 'R': return 'Cancelado';
    }
  }
}

