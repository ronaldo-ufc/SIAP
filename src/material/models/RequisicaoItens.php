<?php
namespace siap\material\models;
use siap\models\DBSiap;
use siap\material\models\Produto;
use siap\auth\models\Autenticador;
use siap\material\models\Requisicao;
use siap\material\models\Estoque;
class RequisicaoItens{
  const ESTATUS_APROVADA = 'A';
  private $requisicao_codigo; 
  private $produto_codigo; 
  private $quantidade;
  private $quantidade_atendida;
  private $quantidade_estornada;
  private $status;
  private $produto;
  private $requisicao;
  private $usuario_estorno;
  private $usuario_recebimento_estorno;
  private function bundle($row){
    $u = new RequisicaoItens();
    $u->setRequisicao_codigo($row['requisicao_codigo']);
    $u->setProduto_codigo($row['produto_codigo']);
    $u->setQuantidade($row['quantidade']);
    $u->setStatus($row['status']);
    $u->setQuantidade_atendida($row['quantidade_atendida']);
    $u->setQuantidade_estornada($row['quantidade_estornada']);
    $u->setUsuario_estorno($row['usuario_estorno']);
    $u->setUsuario_recebimento_estorno($row['usuario_recebimento_estorno']);
    #Objetos
    $u->setProduto(Produto::getById($row['produto_codigo']));
    $u->setRequisicao(Requisicao::getByCodigo($row['requisicao_codigo']));
    return $u;
  }
  
  static function create($requisicao_codigo, $produto, $quantidade){
    $sql = "INSERT INTO siap.requisicao_item (requisicao_codigo, produto_codigo, quantidade) VALUES (?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao_codigo, $produto, $quantidade));
    return $stmt->errorInfo();
  }
  
   static function estorno($requisicao_codigo, $item, $quantidade){
    $aut = Autenticador::instanciar();
    $sql = "UPDATE siap.requisicao_item SET quantidade_estornada = ?, usuario_estorno = ?, data_estorno = current_timestamp WHERE requisicao_codigo = ? and  produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($quantidade,$aut->getUsuario(), $requisicao_codigo, $item));
    return $stmt->errorInfo();
  }
  
  static function delete($requisicao, $produto){
    $sql = "DELETE FROM siap.requisicao_item WHERE requisicao_codigo = ? and  produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao, $produto));
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
  
   static function getByRequisicaoAndProduto($requisicao, $produto){
    $sql = "SELECT * FROM siap.requisicao_item where requisicao_codigo = ? and produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($requisicao, $produto));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
   static function AprovarRecebimento($requisicao, $produto_codigo, $usuario){
     $almoxarifado = 22;
     //  #Inicio de uma transação
    DBSiap::getSiap()->beginTransaction();
    
    #cria os objetos necessários
    #
    $item = RequisicaoItens::getByRequisicaoAndProduto($requisicao, $produto_codigo);
    $req = Requisicao::getByCodigo($requisicao);
    
    #dar entrada no estoque
    #

      Estoque::saida($requisicao, $produto_codigo , $item->getQuantidade_estornada(), $usuario, $item->getUsuario_estorno(), $req->getDestino()->getSetor_id(), $almoxarifado);

      #Aprova a requisição
      $sql = "UPDATE siap.requisicao_item SET usuario_recebimento_estorno = ?, data_recebimento_estorno = current_timestamp WHERE requisicao_codigo = ? and produto_codigo = ?";
      $stmt = DBSiap::getSiap()->prepare($sql);
      $stmt->execute(array($usuario, $requisicao, $produto_codigo));

  
    //  #fim de uma transação
    $msg = $stmt->errorInfo();

    if ($msg[2]){

      DBSiap::getSiap()->rollBack();

    }else{
      DBSiap::getSiap()->commit();
    }

    return $stmt->errorInfo();
  }
  
   static function getAllEstornos(){
    $sql = "select * from siap.requisicao_item where quantidade_estornada is not null and usuario_recebimento_estorno is null";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
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
  public function getQuantidade_estornada() {
    return $this->quantidade_estornada;
  }

  public function setQuantidade_estornada($quantidade_estornado) {
    $this->quantidade_estornada = $quantidade_estornado;
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
  public function getRequisicao() {
    return $this->requisicao;
  }

  public function setRequisicao($requisicao) {
    $this->requisicao = $requisicao;
  }
  public function getUsuario_estorno() {
    return $this->usuario_estorno;
  }
  public function getUsuario_recebimento_estorno() {
    return $this->usuario_recebimento_estorno;
  }

  public function setUsuario_recebimento_estorno($usuario_recebimento_estorno) {
    $this->usuario_recebimento_estorno = $usuario_recebimento_estorno;
  }

    public function setUsuario_estorno($usuario_estorno) {
    $this->usuario_estorno = $usuario_estorno;
  }

      public function setQuantidade_atendida($quantidade_atendida) {
    $this->quantidade_atendida = $quantidade_atendida;
  }
  
  public function showBtnEstorno() {
    $aut = \siap\auth\models\Autenticador::instanciar();
    $requisicao = $this->getRequisicao();
    if ($requisicao->getStatus() == self::ESTATUS_APROVADA and $requisicao->getUsuario_recebimento() and !$this->getUsuario_recebimento_estorno() and $aut->getUsuario() == $requisicao->getUsuario_login()){
      return 'inline';
    }
    return 'none';
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

