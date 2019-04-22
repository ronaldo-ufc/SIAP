<?php
namespace siap\material\models;
use siap\models\DBSiap;
use siap\material\models\RequisicaoItens;

class Requisicao{
  const ESTATUS_ENVIADA = 'E';
  const ESTATUS_CANCELADA = 'R';
  const ESTATUS_NAOENVIADA = 'C';
  const ESTATUS_APROVADA = 'A';
  private $requisicao_codigo; 
  private $numero;
  private $status;
  private $setor_origem;
  private $setor_destino;
  private $data;
  private $usuario_login;
  private $usuario;
  private $origem;
  private $destino;
  
  
  private function bundle($row){
    $u = new Requisicao();
    $u->setRequisicao_codigo($row['requisicao_codigo']);
    $u->setNumero($row['numero']);
    $u->setData($row['data']);
    $u->setStatus($row['status']);
    $u->setSetor_origem($row['setor_origem']);
    $u->setSetor_destino($row['setor_destino']);
    $u->setUsuario_login($row['usuario_login']);
    
    $u->setUsuario(\siap\usuario\models\Usuario::getByLogin($row['usuario_login']));
    $u->setDestino(\siap\setor\models\Setor::getById($row['setor_destino']));
    
    return $u;
  }
  
  static function create($usuario, $origem, $destino){
    $sql = "select * from siap.gera_solicitacao(?,?,?,?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(date('Y'), $origem, $destino, $usuario));
    return $stmt->errorInfo();
  }
  
  static function enviar($codigo){
    $sql = "UPDATE siap.requisicao SET status = ? WHERE requisicao_codigo =?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(self::ESTATUS_ENVIADA, $codigo));
    return $stmt->errorInfo();
  }
  
  static function isItens($requisicao){
    if (RequisicaoItens::getByRequisicao($requisicao)){
      return true;
    }
    return false;
  }
  
  static function Aprovar($requisicao){
    #Aprova a requisição
    $sql = "UPDATE siap.requisicao SET status = ? WHERE requisicao_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(self::ESTATUS_APROVADA, $requisicao));
    return $stmt->errorInfo();
  }
  
  static function delete($codigo){
    $sql = "DELETE FROM siap.requisicao WHERE requisicao_codigo = ? and status = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($codigo, self::ESTATUS_NAOENVIADA));
    return $stmt->errorInfo();
  }

  static function getAllBySetor($setor_codigo){
    $sql = "SELECT * FROM siap.requisicao where setor_destino = ? order by status asc, requisicao_codigo desc";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($setor_codigo));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getAllEnviadas(){
    $sql = "SELECT * FROM siap.requisicao WHERE data >= CURRENT_DATE - 90 and status = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(self::ESTATUS_ENVIADA));
    
    $rows = $stmt->fetchAll();
   // return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getAllByFiltro($numero, $status){
    $sql = "SELECT * FROM siap.requisicao WHERE data >= CURRENT_DATE - 365 and status like '$status'";
    if($numero){
        $sql = $sql." AND numero like '%$numero%' ";
    }

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    
    $rows = $stmt->fetchAll();

    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getByCodigo($codigo){
    $sql = "SELECT * FROM siap.requisicao where requisicao_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($codigo));
    $row = $stmt->fetch();
   //return $stmt->errorInfo();
    if ($row == null){
      return false;
    }
    
    return self::bundle($row);
  }
  
  public function getRequisicao_codigo() {
    return $this->requisicao_codigo;
  }

  public function getNumero() {
    return $this->numero;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getSetor_origem() {
    return $this->setor_origem;
  }

  public function getSetor_destino() {
    return $this->setor_destino;
  }

  public function getData() {
    return $this->data;
  }

  public function getUsuario_login() {
    return $this->usuario_login;
  }

  public function setRequisicao_codigo($requisicao_codigo) {
    $this->requisicao_codigo = $requisicao_codigo;
  }

  public function setNumero($numero) {
    $this->numero = $numero;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function setSetor_origem($setor_origem) {
    $this->setor_origem = $setor_origem;
  }

  public function setSetor_destino($setor_destino) {
    $this->setor_destino = $setor_destino;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function setUsuario_login($usuario_login) {
    $this->usuario_login = $usuario_login;
  }
  public function getUsuario() {
    return $this->usuario;
  }

  public function getOrigem() {
    return $this->origem;
  }

  public function getDestino() {
    return $this->destino;
  }

  public function setUsuario($usuario) {
    $this->usuario = $usuario;
  }

  public function setOrigem($origem) {
    $this->origem = $origem;
  }

  public function setDestino($destino) {
    $this->destino = $destino;
  }
  
  public function haveRequisicaoAberta($setor){
    $sql = "SELECT * FROM siap.requisicao WHERE DATE_PART('YEAR', data) = ? and status = ? and setor_destino = ? order by requisicao_codigo desc limit 1";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(date('Y'), self::ESTATUS_NAOENVIADA, $setor));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }

  public function getStatusNome(){
    switch ($this->status){
      case 'C': return 'Não enviada';
      case 'E': return 'Enviada';
      case 'A': return 'Aprovada';
      case 'R': return 'Cancelada';
    }
  }
  
  public function getStatusClass(){
    switch ($this->status){
      case self::ESTATUS_NAOENVIADA: return 'default';
      case self::ESTATUS_ENVIADA: return 'warning';
      case self::ESTATUS_APROVADA: return 'success';
      case self::ESTATUS_CANCELADA: return 'danger';  
    }
  }
  
  public function getStatusDisplay(){
    switch ($this->status){
      case self::ESTATUS_NAOENVIADA: return 'relative';
      case self::ESTATUS_ENVIADA: return 'none';
      case self::ESTATUS_APROVADA: return 'none';
      case self::ESTATUS_CANCELADA: return 'none';  
    }
  }

}

