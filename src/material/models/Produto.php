<?php
namespace siap\material\models;
use siap\models\DBSiap;
use siap\cadastro\models\Unidade;
use siap\cadastro\models\Grupo;

class Produto {
  private $produto_codigo;
  private $codigo_ufc; 
  private $nome; 
  private $observacao;
  private $status;
  private $unidade_codigo; 
  private $grupo_codigo;
  private $codigo_barras;
  private $quantidade_minima;
  private $imagem;
  private $localizacao;

  private $unidade;
  private $grupo;
  public static $quantidade = 0;
  private static $vlr_uni = 0;


  private function bundle($row){
    $u = new Produto();
    $u->setProduto_codigo($row['produto_codigo']);
    $u->setCodigo_ufc($row['codigo_ufc']);
    $u->setNome($row['nome']);
    $u->setObservacao($row['observacao']);
    $u->setStatus($row['status']);
    $u->setUnidade_codigo($row['unidade_codigo']);
    $u->setGrupo_codigo($row['grupo_codigo']);
    $u->setCodigo_barras($row['codigo_barras']);
    $u->setQuantidade_minima($row['quantidade_minima']);
    $u->setImagem($row['imagem']);
    $u->setLocalizacao($row['localizacao']);
    
    #Objetos de referência
    $u->setUnidade(Unidade::getById($row['unidade_codigo']));
    $u->setGrupo(Grupo::getById($row['grupo_codigo']));
    return $u;
  }
  
  static function getAllByParams($c_ufc, $c_barras, $nome, $unidade, $grupo, $observacao){
    $nome = "%".$nome."%";
    $observacao = "%".$observacao."%";
    $unidade = "%".$unidade."%";
    $grupo = "%".$grupo."%";
    $c_ufc = $c_ufc? "%".$c_ufc."%": "%";
    $c_barras = $c_barras? "%".$c_barras."%": "%";
    $sql = "select * from siap.produto where codigo_ufc like ? and "
            . "codigo_barras like ? and "
            . "nome ilike ? and "
            . "cast (unidade_codigo as text) like ? and "
            . "cast (grupo_codigo as text) like ? and "
            . "observacao ilike ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($c_ufc, $c_barras, $nome, $unidade, $grupo, $observacao));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getById($id){
    $sql = "select * from siap.produto where produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  static function getAllByNome($nome){
    $nome = "%".$nome."%";
    $sql = "select * from siap.produto where nome ilike ? and status = 'A' order by nome asc limit 6";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($nome));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }

  static function create($c_ufc, $c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $localizacao){
    $sql = "INSERT INTO siap.produto (codigo_ufc, nome, observacao, unidade_codigo, grupo_codigo, codigo_barras, quantidade_minima, localizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($c_ufc, $nome, strtoupper(tirarAcentos($observacao)), $unidade, $grupo, $c_barras, $quantidade_minima,$localizacao));
    return $stmt->errorInfo();
  }
  
  static function update($c_ufc, $c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $imagem, $localizacao, $status, $produto_codigo){
    $sql = "UPDATE siap.produto SET codigo_ufc=?, nome=?, observacao=?, unidade_codigo=?, grupo_codigo=?, codigo_barras=?, quantidade_minima=?, imagem = ?, localizacao=?, status=? WHERE produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($c_ufc, $nome, strtoupper(tirarAcentos($observacao)), $unidade, $grupo, $c_barras, $quantidade_minima, $imagem, $localizacao, $status, $produto_codigo));
    return $stmt->errorInfo();
  }
  
  static function delete($produto_codigo){
    $sql = "DELETE FROM siap.produto WHERE produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($produto_codigo));
    return $stmt->errorInfo();
  }
  
  static function getAll(){
    $sql = "select * from siap.produto order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  public function getProduto_codigo() {
    return $this->produto_codigo;
  }

  public function getCodigo_ufc() {
    return $this->codigo_ufc;
  }

  public function getNome() {
    return $this->nome;
  }

  public function getObservacao() {
    return $this->observacao;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getUnidade_codigo() {
    return $this->unidade_codigo;
  }

  public function getGrupo_codigo() {
    return $this->grupo_codigo;
  }

  public function setProduto_codigo($produto_codigo) {
    $this->produto_codigo = $produto_codigo;
  }

  public function setCodigo_ufc($codigo_ufc) {
    $this->codigo_ufc = $codigo_ufc;
  }

  public function setNome($nome) {
    $this->nome = $nome;
  }

  public function setObservacao($observacao) {
    $this->observacao = $observacao;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function setUnidade_codigo($unidade_codigo) {
    $this->unidade_codigo = $unidade_codigo;
  }

  public function setGrupo_codigo($grupo_codigo) {
    $this->grupo_codigo = $grupo_codigo;
  }
  
  public function getUnidade() {
    return $this->unidade;
  }
  public function getImagem() {
    return $this->imagem;
  }

  public function setImagem($imagem) {
    $this->imagem = $imagem;
  }

    public function getGrupo() {
    return $this->grupo;
  }

  public function setUnidade($unidade) {
    $this->unidade = $unidade;
  }

  public function setGrupo($grupo) {
    $this->grupo = $grupo;
  }
  public function getCodigo_barras() {
    return $this->codigo_barras;
  }
  public function getLocalizacao() {
    return $this->localizacao;
  }

  public function setLocalizacao($localizacao) {
    $this->localizacao = $localizacao;
  }

  
  public function setCodigo_barras($codigo_barras) {
    $this->codigo_barras = $codigo_barras;
  }
  public function get_Quantidade(){
    $almoxarifado = 22;
    $sql = "select * from siap.item_quantidade(?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($this->produto_codigo, $almoxarifado));
    $row = $stmt->fetch();
    if ($row == null){
      return 0;
    }
    
    return $row['item_quantidade'];
  }
  private function getVlr_unitario($setor){
    $sql = "select * from siap.media_estoque(?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($this->produto_codigo, $setor));
    $row = $stmt->fetch();
    if ($row == null){
      return 0;
    }
    
    return $row['media_estoque'];
  }
  
  public function getvlr_uni($setor = 22){
        if ($this->vlr_uni == 0) {
            $this->vlr_uni = self::getVlr_unitario($setor);
        }
        return moeda($this->vlr_uni);
  }

  public function getQuantidade() {
    if ($this->quantidade == 0) {
      $this->quantidade = $this->get_Quantidade();
    }
    return $this->quantidade;
  }
       
  public function getStatusNome(){
    switch ($this->status){
      case 'A': return 'Ativo';
      case 'I': return 'Inativo';  
    }
  }
  public function getQuantidade_minima() {
    return $this->quantidade_minima;
  }

  public function setQuantidade_minima($quantidade_minima) {
    $this->quantidade_minima = $quantidade_minima;
  }

  public function getCorClassQuantidadeMinima(){
    if ($this->getQuantidade() < $this->quantidade_minima){
      return 'danger';
    }
    if ($this->getQuantidade() == $this->quantidade_minima){
      return 'warning';
    }
  }
}

